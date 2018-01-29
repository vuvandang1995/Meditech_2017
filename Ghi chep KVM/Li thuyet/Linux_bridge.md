## Phân tích đường đi gói tin từ VM ra ngoài Internet

### Cho mô hình sau:

<img src="https://i.imgur.com/PPKelUj.png">

- Từ mô hình trên ta có một `KVM server`, trong đó có 2 máy ảo được tạo ra là **VM1** và **VM2**.
- `KVM server` sử dụng `Linux Bridge` để tạo ra một switch ảo là `Br0`. `Br0` sẽ được nối tới card mạng thật `ens3` của KVM server.
- Card mạng thật `ens3` của KVM server lúc này sẽ được ngắt địa chỉ IP, thay vào đó cấu hình để `Br0` sử dụng địa chỉ IP của `ens3`. Có thể nói `Br0` sẽ thay thế vai trò của `ens3` với địa chỉ IP và địa chỉ MAC của `ens3`.
- 2 máy ảo **VM1** và **VM2** đi ra ngoài mạng thông qua `Br0` đó.
- `KVM server` được kết nối tới một router là DHCP server và là gateway để đi ra Internet

### Phân tích
- Mỗi VM trong KVM server được tạo ra một card mạng ảo và gắn với một **port** của switch ảo `Br0`, mỗi port đó có tên riêng. Ví dụ **VM1** gắn với port `vnet0`, **VM2** gắn với port `vnet1`.
- Để in ra tất cả các port có trên `Br0`, sử dụng lệnh

`brctl show`

<img src="https://i.imgur.com/tZ0s5sK.png">

- Để xem tất cả các VM có trên KVM server, sử dụng lệnh

`virsh list`

<img src="https://i.imgur.com/C2AIv11.png">

- Để xem địa chỉ MAC của máy ảo, bạn có thể xem ở file cấu hình của nó và lọc ra địa chỉ MAC. Ví dụ

`cat /etc/libvirt/qemu/generic.xml | grep "mac address"`

<img src="https://i.imgur.com/YFETa1B.png">

- Để biết địa chỉ MAC của VM tương ứng với port nào trên `Br0`, sử dụng lệnh `virsh domiflist [tùy chọn]`, trong đó tùy chọn là hostname VM hoặc là ID của VM. Ví dụ:

`virsh domiflist generic`

<img src="https://i.imgur.com/2j00KWo.png">

- Để biết địa chỉ IP nào tương ứng với địa chỉ MAC của VM, có nhiều cách.
	- Cách 1: Ping toàn mạng để cập nhật `arp table` cho KVM server, từ đó tìm ra IP của các VM thông qua MAC của chúng
		- `fping -c 1 -g 192.168.100.0/24`
		- `arp -a | grep 52:54:00:cd:76:3a`
	- Cách 2: Sử dụng lệnh **Nmap** để quét toàn bộ mạng sẽ show ra đc IP và MAC của tất cả host trong mạng, từ đó lọc thông tin IP từ địa chỉ MAC đã biết (tùy chọn `-B 2` là lấy 3 dòng kể từ kết quả lên trên)
		- `nmap -sP 192.168.100.0/24 | grep "52:54:00:CD:76:3A" -B 2`

		<img src="https://i.imgur.com/tTfrdKJ.png">



### Trường hợp chế độ NAT
Trường hợp VM sử dụng chế độ **NAT**, bạn có thể dụng cách sau để biết địa chỉ IP và MAC của các VM

<img src="https://i.imgur.com/CJwSfZO.png">
