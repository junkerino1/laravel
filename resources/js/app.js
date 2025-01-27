import { createApp, reactive } from 'vue';
import { createMongoAbility } from '@casl/ability';
import { abilitiesPlugin } from '@casl/vue';
import Pusher from 'pusher-js';

import 'primevue/resources/themes/saga-blue/theme.css'; // Theme file
import 'primevue/resources/primevue.min.css'; // PrimeVue core styles
import 'primeicons/primeicons.css'; // PrimeIcons

// Import components
import ProductsList from './components/ProductsList.vue';
import CategoryList from './components/CategoryList.vue';
import CreateProductModel from './components/CreateProductModel.vue';
import CreateCategoryModel from './components/CreateCategoryModel.vue';
import FormContainer from './components/form/FormContainer.vue';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';


Pusher.logToConsole = true;

const pusher = new Pusher('local_key', {
    cluster: 'mt1',            // Matches the cluster in .env
    wsHost: '127.0.0.1',       // Local WebSocket server
    wsPort: 6001,              // Port of WebSocket server
    forceTLS: false,           // Disable TLS for local testing
    disableStats: true,        // Disable Pusher statistics
    enabledTransports: ['ws'], // Use only WebSocket transport
});

// Reactive state for abilities
const state = reactive({
    abilities: createMongoAbility([]),
});

// Fetch user permissions
async function fetchUserPermissions() {
    try {
        const response = await fetch('/api/user-permissions', {
            method: 'GET',
            credentials: 'same-origin',
        });
        if (response.ok) {
            const data = await response.json();
            const fetchedAbilities = data.permissions.map((permission) => ({
                action: permission,
                subject: 'all',
            }));
            setAbilities(fetchedAbilities);
        } else {
            console.error('Failed to fetch permissions');
        }
    } catch (error) {
        console.error('Error fetching permissions:', error);
    }
}

// Update abilities dynamically
function setAbilities(abilities) {

    console.log('Set abilities', abilities);
    state.abilities.update(abilities); // Update CASL abilities
}

var channel = pusher.subscribe('permission-updated');
channel.bind('PermissionUpdated', function(data) {
    console.log('Received event:', data);
    fetchUserPermissions();
});

pusher.connection.bind('state_change', function(state) {
    console.log('Pusher connection state changed:', state);
});

pusher.connection.bind('error', function(error) {
    console.log('Pusher connection error:', error);
});

// Create Vue application
const app = createApp({
    components: {
        ProductsList,
        CategoryList,
        CreateProductModel,
        CreateCategoryModel,
        FormContainer,
        IconField,
        InputIcon,
    },
    mounted() {
        fetchUserPermissions(); // Fetch permissions on app mount
    },
});

// Use CASL abilities plugin
app.use(abilitiesPlugin, state.abilities, {
    useGlobalProperties: true,
});

// Mount the app
app.mount('#app');
