<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BetController extends Controller
{


        public static function getBet($cron = true, $targetDate = '', $targetData = '', $provider_acc='') {
            // $pragmaticplay = new PragmaticPlay();
            $userWallet = new UserWallet();
            $merchant_db_connection = null;

            if($targetDate == '') {
                // $targetDate = Carbon::now()
                //                     ->subMinutes(static::$recordFetchRange)
                //                     ->format('Y-m-d H:i:s');
                if($targetData == PragmaticPlay::$betDataType['live_game']){
                    $targetDate = Carbon::now()
                    ->subMinutes(static::$recordFetchRangeLive)
                    ->format('Y-m-d H:i:s');
                }else{
                    $targetDate = Carbon::now()
                    ->subMinutes(static::$recordFetchRange)
                    ->format('Y-m-d H:i:s');
                }
            }

            if(empty($provider_acc)){
                $provider_acc = array_flip(Product::$provider_wallet)['pragmaticplay_wallet'];
            }

            $apiParams = [
                'dataType' => $targetData,
                'login' => config('app.' . $provider_acc . '_securelogin'),
                'password' => config('app.' . $provider_acc . '_securelogin_hash'),
                'timepoint' => Carbon::createFromFormat('Y-m-d H:i:s', $targetDate)
                                        ->setTimezone('GMT+0')
                                        ->getPreciseTimestamp(3),
            ];
    $queryString = http_build_query($apiParams);
            if($provider_acc == 'pragmaticplayc'){
                $apiUrl = config('app.' . $provider_acc . '_feed_api_url') . '/DataFeeds/gamerounds/finished/?' . $queryString;
            }else{
                $apiUrl = config('app.' . $provider_acc . '_api_url') . '/DataFeeds/gamerounds/finished/?' . $queryString;
            }

            $cooldown = 0;
            $apiStatusCode = '';
            try {
                $client = new \GuzzleHttp\Client([
                    'headers' => [
                        'Content-Type' => 'text/csv',
                        'Connection' => 'close',
                    ],
                    'http_errors' => false,
                    // 'proxy' => [
                    //     'http' => config('app.proxy'),
                    //     'https' => config('app.proxy'),
                    // ],
                ]);

                do{
                $response = $client->request('GET', $apiUrl);

                $data = $response->getBody()->getContents();

                $expData1 = explode("\n", $data);
                $totalExpData1 = count($expData1);
                $csvClear = [];
                sleep(2);
                $cooldown++;
                }while($totalExpData1 <= 1 && $cooldown < 10);

                if(!empty($expData1[2])) {
                    $loopCount = 0;
                    $headCount = 0;
                    $headerData = [];
                    // $csvClear = [];
                    foreach ($expData1 as $key => $value) {
                        $loopCount++;

                        if($loopCount == 1) {
                            continue;
                        } elseif($loopCount == 2) {
                            $headerData = explode(",", $value);
                            $headCount = count($headerData);
                            continue;
                        }

                        $expRowData = explode(",", $value);

                        $rowDataCount = count($expRowData);

                        if(($headCount != $rowDataCount) || empty($value)) {
                            continue;
                        }

                        $csvClear[] = array_combine($headerData, $expRowData);

                        $apiStatusCode = (string)$response->getStatusCode();
                    }
                }
            } catch (\Exception $exception) {
                System::errorstack_email($exception);
            }

            if(strval($apiStatusCode) === strval(static::$apiStatusCodeAry['maintenance'])) {
                return '[PragmaticPlay] API Service Currently Under Maintenance';
            }elseif($apiStatusCode != static::$apiStatusCodeAry['success'] && $apiStatusCode != ''){
                // Send email to notify admin

                System::error_email("[PragmaticPlay] Failed to get game bet - {$apiUrl})", $apiParams, $data, $stack);
            }

            if(!isset($csvClear)) {
                return '[PragmaticPlay] Bet Record Not Found At The Moment - ' . $targetDate;
            }

            $gameType='';
            if($targetData == PragmaticPlay::$betDataType['live_game']){
                $gameType = ProductGame::$game_type['live-dealer'];
            }elseif($targetData == PragmaticPlay::$betDataType['slot_game']){
                $gameType = ProductGame::$game_type['slot'];
            }
            // $apiProductId = ProductGame::where('product_id', $pragmaticplay->productId)
            //                                 ->where('game_type', $gameType)
            //                                 ->first();
            $apiProductGameAry = [];
            $apiUsernameAry = [];

            $notFoundGameAry = [];

            foreach ($csvClear as $betRow) {
                $apiUsernameRow = DB::table('user_product_api_by_merchants')
                                    ->select('merchant', 'user_id', 'product_id', 'user_currency as currency', 'api_currency', 'api_username', 'wallet')
                                    ->where('api_username', $betRow['extPlayerID'])
                                    ->where('wallet', Product::$provider_wallet[$provider_acc])
                                    ->first();

            if(empty($apiUsernameAry[$betRow['extPlayerID']])) {
                    // $apiUsernameRow = UserProductAPI::select('user_product_api.user_id', 'users.currency', 'user_product_api.api_currency')
                    //                                 ->join('users', function ($join) {
                    //                                     $join->on('users.id', '=', 'user_product_api.user_id');
                    //                                 })
                    //                                 ->where('api_username', $betRow['extPlayerID'])
                    //                                 ->where('product_id', $pragmaticplay->productId)
                    //                                 ->first();

                    if(!empty($apiUsernameRow)) {
                        $apiUsernameAry[$betRow['extPlayerID']] = [
                            'userId' => $apiUsernameRow->user_id,
                            'currency' => $apiUsernameRow->currency,
                            'api_currency' => $apiUsernameRow->api_currency,
                        ];
                    } else {
                        continue;
                    }
                }

                // $apiProductId = ProductGame::where('product_id', $pragmaticplay->productId)
                //                             ->where('game_type', $gameType)
                //                             ->first();

                // get db connection name by merchant stored in the user
                $merchant_db_connection = DBConnectionInterface::$merchant_connection[$apiUsernameRow->merchant];
                $apiProductId = DB::connection($merchant_db_connection)
                                    ->table('product_games')
                                    ->where('product_id', $apiUsernameRow->product_id)
                                    ->where('game_type', $gameType)
                                    ->first();

                // define amount
                $betAmount = round($betRow['bet'], 2);
                $returnAmount = round($betRow['win'], 2);
                $jackPotAmount = round($betRow['jackpot'], 2);

                // calculation of winloss amount and return amount
                $returnAmount += $jackPotAmount;
                $winLossAmount = $returnAmount - $betAmount;

                // define bet status is win , draw or loss , default setting is loss and debit
                $betStatus = Bet::$status['loss'];
                $betTransactionType = System::$transaction_type['general-lose-bet'];
                $walletTransactionType = UserWalletHistory::$type['debit'];

                if($betAmount == $returnAmount) {
                    // if bet amount and return amount is same , then is draw
                    $betStatus = Bet::$status['draw'];
                    $betTransactionType = System::$transaction_type['general-draw-bet'];
                    $walletTransactionType = '';

                    // if returnAmount is 0 , then skip
                    if($returnAmount <= 0){
                        continue;
                    }
                } elseif($betAmount < $returnAmount) {
                    // if amount is win , then default setting change to win and credit
                    $betStatus = Bet::$status['win'];
                    $betTransactionType = System::$transaction_type['general-win-bet'];
                    $walletTransactionType = UserWalletHistory::$type['credit'];
                }

                // check bet exists
                // $betExists = Bet::whereRaw('api_bet_id = "'.$betRow['playSessionID'].'"')
                //                 ->where('product_id', $pragmaticplay->productId)
                //                 ->first();

                // use db connection to check if this given bet id exists in member's merchant
                $betExists = DB::connection($merchant_db_connection)
                                ->table('bets')
                                ->where('product_id', $apiUsernameRow->product_id)
                                ->whereRaw('api_bet_id = "'.$betRow['playSessionID'].'"')
                                ->first();

    if($betExists) {
                    continue;
                }

                // // check user info is exist or not , if no , then get user info from database
                // if(empty($apiUsernameAry[$betRow['extPlayerID']])) {
                //     $apiUsernameRow = UserProductAPI::select('user_product_api.user_id', 'users.currency', 'user_product_api.api_currency')
                //                                     ->join('users', function ($join) {
                //                                         $join->on('users.id', '=', 'user_product_api.user_id');
                //                                     })
                //                                     ->where('api_username', $betRow['extPlayerID'])
                //                                     ->where('product_id', $pragmaticplay->productId)
                //                                     ->first();

                //     if(!empty($apiUsernameRow)) {
                //         $apiUsernameAry[$betRow['extPlayerID']] = [
                //             'userId' => $apiUsernameRow->user_id,
                //             'currency' => $apiUsernameRow->currency,
                //             'api_currency' => $apiUsernameRow->api_currency,
                //         ];
                //     } else {
                //         continue;
                //     }
                // }
                $betRow['gameID'] = str_replace('"', '', $betRow['gameID']);
                // check game info is exist or not , if no , then get game info from database
                if(empty($apiProductGameAry[$merchant_db_connection][$betRow['gameID']])) {
                    // $productGameIdRow = ProductGameAPI::select('id', 'product_game_id')
                    //                                     ->where('product_game_id', $apiProductId->id)
                    //                                     ->where('game_code', $betRow['gameID'])
                    //                                     ->first();

                    $productGameIdRow = DB::connection($merchant_db_connection)
                                            ->table('product_game_api')
                                            ->select('product_game_api.id', 'product_game_api.product_game_id', 'product_games.game_type AS product_games_game_type')
                                            ->join('product_games', 'product_games.id', '=', 'product_game_api.product_game_id')
                                            ->join('products','products.id','=','product_games.product_id')
                                            // ->where('product_game_id', $apiProductId->id)
                                            ->where('products.id', $apiUsernameRow->product_id)
                                            ->whereRaw('product_game_api.game_code = "'.$betRow['gameID'].'"')
                                            ->first();

                    if (empty($productGameIdRow)) {
    $productGameIdRow = ProductGameAPI::on($merchant_db_connection)
                                                            ->create([
                                                                'product_game_id' => $apiProductId->id,
                                                                'game_code' =>  $betRow['gameID'],
                                                                'name' => json_encode([
                                                                    'en' => $betRow['gameID'],
                                                                ]),
                                                                'image' => json_encode([]),
                                                                'last_modified_by' => 0,
                                                                'status' => ProductGameAPI::$status['inactive'],
                                                            ]);
                    }

                    $apiProductGameAry[$merchant_db_connection][$betRow['gameID']] = [
                        'id' => $productGameIdRow->id,
                        'product_game_id' => $productGameIdRow->product_game_id,
                    ];
                }

                // check currency decimal unit , e.g. IDR is 1:1000
                $amount_converted = false ;
                if(isset(static::$currencyDecimalUnit[$apiUsernameAry[$betRow['extPlayerID']]['api_currency']])) {
                    // if API currency from Database is IDR , then skip
                    if($apiUsernameAry[$betRow['extPlayerID']]['api_currency'] != 'IDR'){
                        $amount_converted = true ;
                        $betAmount *= static::$currencyDecimalUnit[$apiUsernameAry[$betRow['extPlayerID']]['api_currency']];
                        $returnAmount *= static::$currencyDecimalUnit[$apiUsernameAry[$betRow['extPlayerID']]['api_currency']];
                        $winLossAmount *= static::$currencyDecimalUnit[$apiUsernameAry[$betRow['extPlayerID']]['api_currency']];
                    }
                }
                // if API currency from bets is IDR , then convert
                if($amount_converted == false && $betRow['currency'] == 'IDR2'){
                    $betAmount *= static::$currencyDecimalUnit['IDR2'];
                    $returnAmount *= static::$currencyDecimalUnit['IDR2'];
                    $winLossAmount *= static::$currencyDecimalUnit['IDR2'];
                }

                $turnover_amount = ProductGame::getTurnoverAmount($winLossAmount, $betAmount, $productGameIdRow->product_games_game_type);

                $bet = Bet::on($merchant_db_connection)
                        ->create([
                            'user_id' => $apiUsernameRow->user_id,
                            'product_id' => $apiUsernameRow->product_id,
                            'product_game_id' => $apiProductGameAry[$merchant_db_connection][$betRow['gameID']]['product_game_id'],
                            'product_game_api_id' => $apiProductGameAry[$merchant_db_connection][$betRow['gameID']]['id'],
                            'api_bet_id' => $betRow['playSessionID'],
                            'currency' => $apiUsernameAry[$betRow['extPlayerID']]['currency'],
                            'wallet' => Product::$provider_wallet[$provider_acc],
                            'odds' => null,
                            'turnover_amount' => $turnover_amount,
                            'bet_amount' => $betAmount,
                            'return_amount' => $returnAmount,
                            'win_loss_amount' => $winLossAmount,
                            'status' => $betStatus,
                            'multiple_side_bet' => 0,
                            'data' => json_encode($betRow),
                            'bet_date' => $betRow['endDate'],
                        ]);
    // to update user wallet by merchant id
                if(!empty($walletTransactionType)) {
                    $userWallet->update_wallet(
                        $apiUsernameRow->user_id,
                        $walletTransactionType,
                        $apiUsernameAry[$betRow['extPlayerID']]['currency'],
                        Product::$provider_wallet[$provider_acc],
                        abs($winLossAmount),
                        $merchant_db_connection,
                        $betTransactionType,
                        $bet->id,
                        '',
                        (array) $bet,
                    );
                }

            }

            if(!empty($notFoundGameAry)) {
                $stack = [CLASS, FUNCTION, FILE, LINE];
                System::error_email("[PragmaticPlay] New Game Not Found In Database - '" . $apiUrl . "'", $apiParams, $notFoundGameAry, $stack);
            }

            $client = null;

            return '[PragmaticPlay] Bet Record(s) Has Been Processed Successfully';
        }

        public static function getDailyBetSummary($cron = true, $targetDate = '', $targetData = '', $currency ='', $product = []) {
            $client = static::requestConfiguration();
            $pragmaticplay = new PragmaticPlay($currency, $product);

            $date = Carbon::createFromFormat('Y-m-d', $targetDate)->toDateString();
            $startDate = $date.' 00:00:00';
            $endDate = $date.' 23:59:59';

            $apiParams = [
                'login' => config('app.' . $pragmaticplay->provider_config . '_securelogin'),
                'password' => config('app.' . $pragmaticplay->provider_config . '_securelogin_hash'),
                'startDate' => $startDate,
                'endDate' => $endDate,
                'dataType' => $targetData,
            ];

            $queryString = urldecode(http_build_query($apiParams));

            $apiUrl = config('app.' . $pragmaticplay->provider_config . '_api_url') . '/DataFeeds/totals/daily/?' . $queryString;

            $apiData = [];
            try {
                $response = $client->request('GET', $apiUrl);

                $apiData = json_decode($response->getBody(), true);
            } catch(\Exception $exception) {
                // System::errorstack_email($exception);
            }

            return $apiData ?? [];
        }

        public static function calculate_invoice($currency = '', $from_date = '', $to_date = '', $selected_merchant= [], $wallet = '') {
            $from_date = Carbon::parse($from_date);
            $to_date = Carbon::parse($to_date);
            $pragmaticplay = new PragmaticPlay($currency, null, $wallet);

            $all_queries = [];
            foreach($selected_merchant as $merchant){
                $db_connection = DBConnectionInterface::allowedReplicateConnection($merchant);
                if($db_connection == null){
                    continue;
                }

                $db_name = DB::connection($db_connection)->getDatabaseName();
                // select product game store to array
                $product_games =  DB::connection($db_connection)
                                    ->table($db_name.'.product_games')
                                    ->select('product_games.id', 'product_games.game_type' )
                                    ->leftJoin('products', 'product_games.product_id', '=', 'products.id')
                                    ->where('products.wallet', Product::$provider_wallet[$pragmaticplay->provider_config])
                                    ->get()
                                    ->keyBy('id')->toArray();

                // select blackjack game code store to array
                $blackjack_game_code =  DB::connection($db_connection)
                                    ->table($db_name.'.product_game_api')
                                    ->select( 'id','game_code' )
                                    ->whereIn('game_code', static::$blackjack_gameCode)
                                    ->get()
                                    ->keyBy('id')->toArray();

    $bets =  DB::connection($db_connection)
                            ->table($db_name.'.bets')
                            ->select('bets.currency', 'bets.product_game_id','bets.product_game_api_id', DB::raw('SUM(bet_amount) AS total_bet, SUM(win_loss_amount) AS total_win_loss'))
                            ->where('bets.currency', $currency)
                            ->where('bets.wallet', Product::$provider_wallet[$pragmaticplay->provider_config])
                            ->where('bets.bet_date', '>=', $from_date->format('Y-m-d'))
                            ->where('bets.bet_date', '<=', $to_date->format('Y-m-d').' 23:59:59')
                            ->groupBy('bets.currency')
                            ->groupBy('bets.product_game_id')
                            ->groupBy('bets.product_game_api_id');
                $all_queries[] = $bets;
            }

            $bets = DBConnectionInterface::formulateUnionQueries($all_queries);
            $bets = $bets->get();

            $invoice = [];

            // define invoice report variable ,
            $invoice_game_type = [
                ProductGame::$game_type['slot'],
                ProductGame::$game_type['live-dealer'],
                'blackjack',
            ];
            foreach($invoice_game_type as $invoice_type){
                if(empty($invoice[$currency])){
                    $invoice[$currency] = [];
                }
                if(empty($invoice[$currency][$invoice_type])){
                    if($invoice_type == ProductGame::$game_type['slot']){
                        $invoice[$currency][$invoice_type] = [
                            'range' => $from_date->toFormattedDateString().' - '.$to_date->toFormattedDateString(),
                            'total_bet' => 0,
                            'total_win_loss' => 0,
                            'total_win_loss_USD' => 0,
                            'percent' => 8,
                        ];
                    }elseif($invoice_type == ProductGame::$game_type['live-dealer']){
                        $invoice[$currency][$invoice_type] = [
                            'range' => $from_date->toFormattedDateString().' - '.$to_date->toFormattedDateString(),
                            'total_bet' => 0,
                            'total_win_loss' => 0,
                            'total_win_loss_USD' => 0,
                            'percent' => 10,
                        ];
                    }elseif($invoice_type == 'blackjack'){
                        $invoice[$currency][$invoice_type] = [
                            'range' => $from_date->toFormattedDateString().' - '.$to_date->toFormattedDateString(),
                            'total_bet' => 0,
                            'total_win_loss' => 0,
                            'total_win_loss_USD' => 0,
                            'percent' => 11,
                        ];
                    }

                }
            }
            foreach ($bets as $bet) {
                $game_type = '';
                if(isset($product_games[$bet->product_game_id])){
                    $game_type = $product_games[$bet->product_game_id]->game_type;
                }

                if($game_type == ''){
                    continue;
                }else{
                    $bet->game_type = $game_type;
                }

                if(in_array($bet->product_game_api_id, array_keys($blackjack_game_code))){
                    $bet->game_type = 'blackjack';
                }

                switch ($bet->currency) {
                    case 'IDR':
                        $idr_to_usd_rate = 0.07;
                        break;
                    default:
                        $idr_to_usd_rate = 1;
                        break;
                }

                $total_bet = $bet->total_bet;
                $total_win_loss = -($bet->total_win_loss);
                if(isset(static::$currencyDecimalUnit[$bet->currency])) {
                    $decimalUnit = static::$currencyDecimalUnit[$bet->currency];
                    $total_bet /=  $decimalUnit;
                    $total_win_loss /= $decimalUnit;
                }

    Chris, [1/15/2025 11:29 AM]
    $total_win_loss_USD = $total_win_loss * $idr_to_usd_rate;
                if($bet->game_type == ProductGame::$game_type['slot']){
                    if(!empty($invoice[$bet->currency][$bet->game_type])){
                        $invoice[$bet->currency][$bet->game_type]['total_bet'] += $total_bet;
                        $invoice[$bet->currency][$bet->game_type]['total_win_loss'] += $total_win_loss;
                        $invoice[$bet->currency][$bet->game_type]['total_win_loss_USD'] += $total_win_loss_USD;
                    }
                }else if($bet->game_type == ProductGame::$game_type['live-dealer']){
                    if(!empty($invoice[$bet->currency][$bet->game_type])){
                        $invoice[$bet->currency][$bet->game_type]['total_bet'] += $total_bet;
                        $invoice[$bet->currency][$bet->game_type]['total_win_loss'] += $total_win_loss;
                        $invoice[$bet->currency][$bet->game_type]['total_win_loss_USD'] += $total_win_loss_USD;
                    }
                }else if($bet->game_type == 'blackjack'){
                    if(!empty($invoice[$bet->currency][$bet->game_type])){
                        $invoice[$bet->currency][$bet->game_type]['total_bet'] += $total_bet;
                        $invoice[$bet->currency][$bet->game_type]['total_win_loss'] += $total_win_loss;
                        $invoice[$bet->currency][$bet->game_type]['total_win_loss_USD'] += $total_win_loss_USD;
                    }
                }
            }
            foreach($invoice as $currency_index => $currency_val){
                foreach($currency_val as $game_type_index => $game_type){
                    $invoice[$currency_index][$game_type_index]['share'] = round($game_type['total_win_loss'] * ($game_type['percent'] / 100), 2);
                    $invoice[$currency_index][$game_type_index]['share_USD'] = round($game_type['total_win_loss_USD'] * ($game_type['percent'] / 100), 2);
                }
            }

            return $invoice[$currency] ?? [];
        }

        public static function checkBetGameDetails($bet_id, $merchant, $wallet) {
            $db_connection = DBConnectionInterface::$merchant_replicate_connection[$merchant];
            $client = static::requestConfiguration();

            $bet = Bet::on($db_connection)->find($bet_id);
            $pragmaticplay = new PragmaticPlay($bet->currency, null, $wallet);

            $data = json_decode($bet->data);
            $extPlayerID = $data->extPlayerID;
            $roundId = $data->playSessionID;
            $gameId = $data->gameID;
            $parameter = [
                'secureLogin' => config('app.' . $pragmaticplay->provider_config . '_securelogin'),
                'playerId' => $extPlayerID,
                'gameId' => $gameId,
                'roundId' => $roundId,
            ];

            $parameter['hash'] = static::calculateHash($parameter, $pragmaticplay);
            $queryString = http_build_query($parameter);
            $apiUrl = config('app.' . $pragmaticplay->provider_config . '_api_url') . '/http/HistoryAPI/OpenHistoryExtended/';

            $apiData = [];
            try {
                $response = $client->request('POST', $apiUrl, [
                    'form_params' => $parameter,
                ]);
                $apiData = json_decode($response->getBody(), true);
            } catch(\Exception $exception) {
                // System::errorstack_email($exception);
            }

            $history_url = '';
            if(isset($apiData['error']) && (string)$apiData['error'] === strval(static::$passErrorCode)) {
                $history_url = $apiData['url'];
            } else {
                // $stack = [CLASS, FUNCTION, FILE, LINE];
                // System::error_email("[PragmaticPlay] Failed to get bet history url - {$apiUrl}", $parameter, $apiData, $stack);
            }

            return $history_url ?? '' ;
        }
    }
