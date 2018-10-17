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

- Thttps://www.digitalocean.com/community/tutorials/how-to-create-a-self-signed-ssl-certificate-for-nginx-in-ubuntu-16-04:

# Các bước setup 1 video call app lên server
B1: chuẩn bị: 1 webserver, 1 socket server+peerJS server
b2: Tải source code về webserver. 
  - Chỉnh sửa trong file setting.py : ALLOWED_HOSTS, Databases
  - Chỉnh sửa phần tạo websocket phía client: khi tạo biến websocket,thay `ws`thành `wss`
  - Sửa đường dẫn file .js chứa biến peer khi gọi video call 1-1: sửa địa chỉ IP của PeerJS server và port chỉnh là port được accept trong file cấu hình `stunnel/dev_https`
  - Chỉnh sửa đường dẫn trong file html mà load file `socket.io/socket`
  - Chỉnh sửa đường dẫn trong file chứa `connection.socketURL = ... `
  - Bật redis server trên webserver
B3: Lưu ý rằng trong file `stunnel/dev_htpps` chứa confile để chạy https.

```
pid=

cert = stunnel/stunnel.pem
sslVersion = TLSv1.2
foreground = yes
output = stunnel.log

[https]
accept=8444
connect=9001
TIMEOUTclose=1
```

- Trong đó, 3 dòng cuối có ý nghĩa là các máy client khi kết nối tới PeerJS server thì phải vào bằng port 8444, khi vào tới server sẽ được định hướng chuyển sang port 9001 (port để tạo biến Peer). Rõ hơn là phía webserver, chỗ tạo biến Peer thì chỉnh port là 8444
- Riêng socket server thì không cần tạo thư mục stunnel, chỉnh cần chỉnh sửa trong file `RTCMultiConnection-master/config.json` là sẽ có kết nối https hoặc http thường tùy cấu hình trong `config.json`

- Để chạy các port peerJS và socket io server cùng lúc thì nên dụng supervisor
**Cài supervisor**
1. `apt-get update && apt-get install -y supervisor`
2. tạo file log: `mkdir -p /var/log/supervisor`
3. Viết file `/etc/supervisor/conf.d/supervisord.conf`

```
[supervisord]
nodaemon=true


[program:stunnel]
directory = /home/osticket
command= stunnel4 stunnel/dev_https

[program:https]
command= HTTPS="1"

[program:https1]
command= /usr/local/bin/peerjs --port 9001


[program:http]
command= /usr/local/bin/peerjs --port 9001


[program:sockethttps]
directory = /home/osticket/RTCMultiConnection-master
command= node server.js

startretries=5
```
4. Bật supervisor: `systemctl start supervisord`
