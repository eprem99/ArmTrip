import '../bootstrap';

import { createApp } from 'vue';
import ExampleComponent from './components/ExampleComponent.vue';

const el = document.getElementById('app');

if (el) {
    createApp(ExampleComponent).mount(el);
}
