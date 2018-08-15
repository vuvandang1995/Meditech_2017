## Các bước cài đặt socket server để share screen
- b1: cài `npm install socketio-over-nodejs`
- b2: cài `npm install node-static`
- b3: chạy server chế độ http:

```
cd node_modules/socketio-over-nodejs/
node signaler.js
```

- b4: chạy server chế độ https:

```
cd node_modules/socketio-over-nodejs/
node signaler-ssl.js
```
- b5: Mặc định nó chạy trên port **8888**. có thể custem trong file `signaler.js`. test thử

`http://localhost:8888` hoặc `https://localhost:8888`
