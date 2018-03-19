# Environment
- Phần này giải thích các cấu hình node Controller và node Compute
- Hầu hết các môi trường bao gồm Identity service, Image service, Compute, Networking. Còn Dashboard, Block Storage, Object Storage và các thành phần khác thì tùy chọn
- Yêu cầu tối thiểu để sử dụng với những OS nhỏ nhẹ như cirros:
	- Controller node: 1 Processor, 4GB memory, 5GB storage
	- Compute node: 1 Processor, 4GB memory, 10 Storage
- Nếu hiệu suất giảm khi enable thêm các service hoặc máy ảo, khi đó hãy xem xét bổ sung phần cứng
- Một phân vùng disk trên mỗi node được sử dụng cho các cài đặt cơ bản. Tuy nhiên, bạn nên xem xét LVM cho việc cài các service tùy chọn như Block Storage
- Cho lần cài đầu tiên, nhiều người sẽ chọn cách cài trên máy ảo vò những ưu điểm của nó nhưng nó cũng làm giảm hiệu suất của  instance

# Security
- Các service OpenStack hỗ trợ service theo các phương thức như password, policy, encryption
- Để đơn giản quá trình cài đặt, hướng dẫn này chỉ bảo mật theo password
- Password có thể tạo bằng tay, nhưng chuỗi kết nối database trong file cấu hình không được chứa kí tự đặc biệt như "@"/
- Sử dụng pwgen hoặc dùng lệnh sau để tạo password ngẫu nhiên

`openssl rand -hex 10`

- Trong bài hướng dẫn này sử dụng SERVICE_PASS để sử dụng password các service, SERVICE_DBPASS sử dụng cho password của database
- Đây là các biến pass và tôi thay thế tất cả thành `ok123` cho dễ nhớ.

<img src="">

# Host networking
- Sau khi cài đặt OS trên mỗi node, bạn phải cấu hình network interface

<img src="">

<img src="">

# Network Time Protocol (NTP)
- Chúng ta cần đồng bộ thời gian cho các node. Để cài đặt NTP, ta sử dụng Chrony
## Trên controller
- Cài đặt gói chrony cho NTP server

`apt install -y chrony`

- Sao lưu cấu hình của NTP server

`cp /etc/chrony/chrony.conf /etc/chrony/chrony.conf.orig`

- Cấu hình NTP server

```
echo "server 1.vn.pool.ntp.org iburst 
server 0.asia.pool.ntp.org iburst 
server 3.asia.pool.ntp.org iburst

allow 10.10.10.0/24" >> /etc/chrony/chrony.conf
```

- Khởi động lại dịch vụ NTP trên Controller

`systemctl restart chrony`

- Kiểm tra dịch vụ NTP đã hoạt động hay chưa

`chronyc sources`

- Output

```
210 Number of sources = 8
MS Name/IP address         Stratum Poll Reach LastRx Last sample
===============================================================================
^* time.vng.vn                   3   6    77    34  +1360us[+2949us] +/-   81ms
^- mta.khangthong.net            2   6    77    34    -42ms[  -42ms] +/-  260ms
^? 2001:b030:242b:ff00::1        0   6     0   10y     +0ns[   +0ns] +/-    0ns
^? makaki.miuku.net              0   6     0   10y     +0ns[   +0ns] +/-    0ns
^? x.ns.gin.ntt.net              0   6     0   10y     +0ns[   +0ns] +/-    0ns
^? 2001:df1:801:a005:3::1        0   6     0   10y     +0ns[   +0ns] +/-    0ns
^- send.mx.cdnetworks.com        2   6    77    34  +1829us[+1829us] +/-  207ms
^+ pontoon.latt.net              3   6    77    33   +490us[ +490us] +/-  152ms
```

- Tất cả các node khác tham khảo node Controller để đồng bộ đồng hồ. thực hiện tương tự trên các node khác.
- Thay vì echo các server ntp.org thì comment dòng:

`pool 2.debian.pool.ntp.org offline iburst`

và thêm:

`server controller iburst`

- Sau đó restart lại dịch vụ và kiểm tra:

```
root@compute:~# service chrony restart
root@compute:~# chronyc sources
210 Number of sources = 1
MS Name/IP address         Stratum Poll Reach LastRx Last sample
===============================================================================
^? controller                    0   6     0   10y     +0ns[   +0ns] +/-    0ns
```


