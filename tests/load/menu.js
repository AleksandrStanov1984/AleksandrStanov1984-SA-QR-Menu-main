import http from 'k6/http';
import { sleep } from 'k6';

export let options = {
    vus: 50, // 50 пользователей
    duration: '30s',
};

export default function () {
    http.get('http://localhost:8000/r/panino-pizza-test');
    sleep(1);
}
