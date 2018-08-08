## Triển khai
1. Đầu tiên cần cài đặt socker server để  các connection kết nối tới (cái này chưa rõ cơ chế lắm)
  - B1: tải file này về: `wget https://github.com/muaz-khan/RTCMultiConnection/archive/master.zip`
  - B2: Giải nén: `unzip master.zip`
  - B3: Chạy lệnh setup: `npm install rtcmulticonnection --production`
  - B4: di chuyển vào thư mục `rtcmulticonnection` và chạy file `server.js` bằng nodejs: `nodejs server.js`
2. tải source code về và thêm các đường dẫn quan trọng sau
  - <script src="{% static 'peer/RTCMultiConnection.min.js' %}"></script>
  - <script src="http://localhost:9001/socket.io/socket.io.js"></script>
3. Thay lại dòng `connection.socketURL = "http://localhost:9001/";` ở trong source code cho đúng đường dẫn

4. Có rất nhiều thứ hay ho ở đây:
https://github.com/muaz-khan/RTCMultiConnection/blob/master/demos/Video-Conferencing.html

## Quan trọng: 
**Để truyền được dữ liệu camera, microphone và screen sharing thì phải dùng https**
- link hướng dẫn setup socket server https đây: https://github.com/muaz-khan/RTCMultiConnection/blob/master/docs/installation-guide.md
- nếu làm ứng dụng video call sử dụng Peerjs thì cũng phải để PeeJS server cho phép lấy ID bằng https. cái này mai note tiếp.
- *Link setup https*: https://stackoverflow.com/questions/8023126/how-can-i-test-https-connections-with-django-as-easily-as-i-can-non-https-connec
- Để tạo được websocket trên https thì sửa `ws`thành `wss` ở chỗ tạo object Websocket phía client javascript.
- Cần cấu hình nginx hay cái gì đó để ứng dụng django-python hay gì đó chạy HTTPS mới video call đc.
- Mai note tiếp
