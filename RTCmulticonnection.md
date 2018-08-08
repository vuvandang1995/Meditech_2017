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
