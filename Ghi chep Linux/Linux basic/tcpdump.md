## Giới thiệu
- Tcpdump là phần mềm bắt gói tin trong mạng làm việc trên hầu hết các phiên bản hệ điều hành unix/linux. Tcpdump cho phép bắt và lưu lại những gói tin bắt được, từ đó chúng ta có thể sử dụng để phân tích.

## 15 lệnh TCPDUMP được sử dụng trong thực tế
### 1. Bắt gói tin từ một giao diện ethernet cụ thể thông qua tcpdump -i
- Khi bạn thực thi lệnh tcpdumpmà không có tùy chọn cụ thể, nó sẽ bắt tất cả các gói tin lưu thông qua card mạng. Tùy chọn -i sẽ cho phép bạn lọc một Interface (giao diện/card mạng) ethernet cụ thể.

<img src="https://i.imgur.com/GloNpt0.png">

### 2. Chỉ bắt số lượng N gói tin thông qua lệnh tcpdump -c
- Khi bạn thực thi lệnh tcpdump, nó sẽ thực hiện đến khi bạn hủy bỏ lệnh. Sử dụng tùy chọn -c bạn sẽ có thể lựa chọn cụ thể số lượng gói tin được bắt.

<img src="https://i.imgur.com/ffurGMd.png">

### 3. Hiển thị các gói tin được bắt trong hệ ASCII thông qua tcpdump -A
- Dưới đây là lệnh tcpdump hiển thị gói tin dưới dạng ASCII.

<img src="https://i.imgur.com/WpRx1Q8.png">

### 4. Hiển thị các gói tin được bắt dưới dạng HEX và ASCII thông qua tcpdump -XX
- Một vài người dùng có thể muốn phân tích gói tin dưới dạng giá trị cơ số 16. tcpdump cung cấp một cách hiển thị gói tin dưới cả hai dạng ASCII và HEX.

<img src="https://i.imgur.com/dfGJWcW.png">

### 5. Bắt gói tin và ghi vào một file thông qua tcpdump -w
- tcpdump cho phép bạn lưu gói tin thành một file, và sau đó bạn có thể sử dụng với mục đích phân tích khác.

<img src="https://i.imgur.com/r3IpDDr.png">

- Tùy chọn -w ghi các gói tin vào một file cho trước. Phần mở rộng của file nên là .pcap để có thể đọc được bởi các phần mềm phân tích giao thức mạng.

### 6. Đọc các gói tin từ một file thông qua tcpdump -r
- Bạn có thể đọc được các file .pcap như sau:

<img src="https://i.imgur.com/xQimbdw.png">

### 7.Bắt các gói tin với địa chỉ IP thông qua tcpdump -n
- Trong các ví dụ phía trên hiển thị gói tin với địa chỉ DNS chứ không phải địa chỉ IP/ Ví dụ dưới đây bắt các gói tin và hiển thị địa chỉ IP của thiết bị liên quan.

<img src="https://i.imgur.com/5fFzhIo.png">

### 8. Bắt các gói tin với các dấu thời gian thông quan tcpdump -tttt

<img src="https://i.imgur.com/u0cGk1k.png">

### 9. Đọc các gói tin lớn hơn N byte
- Bạn có thể chỉ nhận những gói tin lớn hơn N byte thông qua một bộ lọc “greater”.

<img src="https://i.imgur.com/BSAUGnC.png">

### 10. Chỉ nhận những gói tin trong với một kiểu giao thức cụ thể.
- Bạn có thể lọc các gói tin dựa vào kiểu giao thức. Bạn có thể chọn một trong những giao thức — fddi, tr, wlan, ip, ip6, arp, rarp, decnet, tcp và udp. Ví dụ dưới đây chỉ bắt các gói tin arp thông qua giao diện eth0.

<img src="https://i.imgur.com/YgfYBgb.png">

### 11. Đọc các gói tin nhỏ hơn N byte.
- Bạn có thể chỉ nhận những gói tin nhỏ hơn N byte thông qua bộ lọc “less”.

<img src="https://i.imgur.com/SKUBSba.png">

### 12. Nhận các gói tin trên một cổng cụ thể thông qua tcpdump port.
- Nếu bạn muốn biết tất cả gói tin nhận được trên một cổng cụ thể trên thiết bị, bạn có thể sử dụng lệnh như sau

<img src="https://i.imgur.com/BstEBtr.png">

### 13. Bắt các gói tin trên địa chỉ IP và cổng đích.
- Các gói tin có địa chỉ IP và cổng nguồn và đích. Sử dụng tcpdump chúng ta có thể áp dụng bộ lọc trên địa chỉ IP và cổng nguồn hoặc đích. Lệnh dưới đây bắt các gói tin trong eth0 với địa chỉ đích IP và cổng 22.

<img src="https://i.imgur.com/TiBAqyq.png">

### 14. Bắt các gói tin kết nối TCP giữa hai host.
- Nếu hai tiến trình từ hai thiết bị kết nối thông qua giao thức TCP, chúng ta sẽ có thể bắt những gói tin thông qua lệnh dưới đây:

<img src="https://i.imgur.com/z9xgqvJ.png">

Bạn có thể mở file comm.pcap để debug bất cứ vấn đề tiềm tàng nào.

### 15. Bộ lọc gói tin tcpdump – Bắt tất cả các gói tin ngoại trừ arp và rarp
- Trong lệnh tcpdump bạn có thể sử dụng điều kiện “and”, “or” hoặc “not” để lọc các gói tin.

<img src="https://i.imgur.com/4jiy9a1.png">

