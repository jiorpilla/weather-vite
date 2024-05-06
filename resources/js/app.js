import './bootstrap';


import { createApp } from "vue";
import App from  "./App.vue";


import { library } from '@fortawesome/fontawesome-svg-core';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { fas } from '@fortawesome/free-solid-svg-icons';
import { far } from '@fortawesome/free-regular-svg-icons';
import { fab } from '@fortawesome/free-brands-svg-icons';

// Add the icons you need to the library
library.add(fas, far, fab);

const app = createApp(App);

// Register the FontAwesomeIcon component globally
app.component('font-awesome-icon', FontAwesomeIcon);

app.mount("#app");
