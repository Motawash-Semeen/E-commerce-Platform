import axios from 'axios';

// Inertia sends its requests through axios. Laravel's XSRF-TOKEN cookie is read
// by axios automatically, so CSRF protection works with no extra code.
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
