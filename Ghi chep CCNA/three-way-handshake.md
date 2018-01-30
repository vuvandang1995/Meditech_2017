## Giới thiệu
Ở lớp Transport có 2 giao thức quan trọng là UDP và TCP.

- TCP là giao thức thuộc dạng connection-oriented (hướng kết nối). Có nghĩa là nó thiết lập kênh kết nối trước khi truyển data đi.

- UDP là giao thức thuộc dạng connectionless (nghĩa là không hướng kết nối). Nó không cần thiết lập kênh truyền trước khi truyền dữ liệu đi.

TCP thiết lập kết nối bằng 3 bước bắt tay (3-way handshake)

sender ___________ receiver

SYN seq=X ----------> SYN received (step 1)

SYN received <--------send ACK X+1 and SYN Y (step 2)

Send ACK Y+1 --------> (step 3)

<img src="http://3.bp.blogspot.com/_AamnZyf3C_A/TBvQXqrnqII/AAAAAAAAAY0/lf0Kndz8N8A/s1600/tcp+3+way+handshake.png">

## Cơ chế hoạt động
1. SYN: các chương trình máy con (ví dụ yêu cầu từ browser, ftp client) bắt đầu connection với máy chủ bằng cách gửi một packet với cờ "SYN" đến máy chủ.
SYN packet này thường được gửi từ các cổng cao (1024 - 65535) của máy con đến những cổng trong vùng thấp (1 - 1023) của máy chủ. Chương trình trên máy con sẽ hỏi hệ điều hành cung cấp cho một cổng để mở connection với máy chủ. Những cổng trong vùng này được gọi là "cổng máy con" (client port range). Tương tự như vậy, máy chủ sẽ hỏi HĐH để nhận được quyền chờ tín hiệu trong máy chủ, vùng cổng 1 - 1023. Vùng cổng này được gọi là "vùng cổng dịch vụ" (service port).
Ví dụ (mặc định):

- Web Server sẽ luôn chờ tín hiệu ở cổng 80 và Web browser sẽ connect vào cổng 80 của máy chủ.
- FTP Server sẽ lắng ở port 21.

Ngoài ra trong gói dữ liệu còn có thêm địa chỉ IP của cả máy con và máy chủ.

2. SYN/ACK: khi yêu cầu mở connection được máy chủ nhận được tại cổng đang mở, server sẽ gửi lại packet chấp nhận với 2 bit cờ là SYN và ACK.
SYN/ACK packet được gửi ngược lại bằng cách đổi hai IP của server và client, client IP sẽ thành IP đích và server IP sẽ thành IP bắt đầu. Tương tự như vậy, cổng cũng sẽ thay đổi, server nhận được packet ở cổng nào thì cũng sẽ dùng cổng đó để gửi lại packet vào cổng mà client đã gửi.
Server gửi lại packet này để thông báo là server đã nhận được tín hiệu và chấp nhận connection, trong trường hợp server không chấp nhận connection, thay vì SYN/ACK bits được bật, server sẽ bật bit RST/ACK (Reset Acknowledgement) và gởi ngược lại RST/ACK packet.
Server bắt buộc phải gửi thông báo lại bởi vì TCP là chuẩn tin cậy nên nếu client không nhận được thông báo thì sẽ nghĩ rằng packet đã bị lạc và gửi lại thông báo mới.

3. ACK: khi client nhận được SYN/ACK packet thì sẽ trả lời bằng ACK packet. Packet này được gởi với mục đích duy báo cho máy chủ biết rằng client đã nhận được SYN/ACK packet và lúc này connection đã được thiết lập và dữ liệu sẽ bắt đầu lưu thông tự do.
Đây là tiến trình bắt buộc phải thực hiện khi client muốn trao đổi dữ liệu với server thông qua giao thức TCP. Một số thủ thuật dựa vào đặc điểm này của TCP để tấn công máy chủ (ví dụ DOS).