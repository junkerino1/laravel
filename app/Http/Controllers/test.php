<?php
$client = new Grpc\BaseStub('localhost:50051', [
    'credentials' => Grpc\ChannelCredentials::createInsecure(),
]);
echo "gRPC setup is working!";
