# Tạo VM bằng Virt-manager
- Chuẩn bị
	- Máy chủ đã cài sẵn KVM, virt-manager, Linux Bridge
	- Máy client có phần mềm putty để ssh vào máy chủ KVM, hoặc ssh trực tiếp trên terminal nếu là Ubuntu
- Với Client sử dung putty
### B1: kích hoạt X11 tron putty
Khởi động putty và cấu hình để kích hoạt X11 phía client theo hình các thao tác: Connection => SSH => X11.

<img src="https://i.imgur.com/pgph8BL.png">

### B2: SSH để server
**Note:** Login với tài khoản root (lưu ý, tính năng cho phép ssh bằng root phải được kích hoạt trước) và gõ lệnh dưới để khởi động công cụ quản lý KVM
- Cấu hình cho phép login với root trong file `/etc/ssh/sshd_conf`
`sudo vim /etc/ssh/sshd_config`
- Chỉnh sửa tùy chọn như sau
```
PermitRootLogin yes
PasswordAuthentication yes
```
- Khởi động lại ssh
- Sử dụng putty để ssh vào server
### B3: sử dụng Virt-manager
- Sau khi ssh được vào server, sử dụng lệnh sau để bật giao diện virt-manager
`virt-manager`

<img src="https://i.imgur.com/NUSd3fB.png">

Như trong hình ta thấy máy chủ KVM đang có 2 VM
### B4: Tạo VM từ file ISO hoặc images
- Chọn New và tích tùy chọn

<img src="https://i.imgur.com/LuXoRC8.png">

- Bấm Browse để vào đường dẫn tới file ISO hoặc Images

<img src="https://i.imgur.com/nA15b2g.png">

<img src="https://i.imgur.com/wceHVlO.png">

- Tùy chọn dung lượng RAM và số CPU

<img src="https://i.imgur.com/e9Kz8LN.png">

- Tùy chọn dung lượng disk

<img src="https://i.imgur.com/3Cnbhwi.png">

- Tích vào "Customize configuration before install" để tùy chọn khác trước khi tạo VM sẽ hiện ra giao diện tùy chọn chi tiết cho VM như sau

<img src="https://i.imgur.com/98mT87L.png">

- Bấm vào "Begin installion" để bắt đầu tạo khi đã tùy chọn xong

### B5: Tùy chọn Network cho VM
- Có 3 chế độ network cho VM
	- Bridge public
	- NAT
	- Bridge private
	
### Cài đặt bridge public cho VM
**Mục đích:** Cho phép các VM cùng dải mạng với máy chủ KVM
- Tích đúp vào một VM cần cài sẽ hiện ra một giao diện quản lý VM đó
- Bấm tùy chọn "Details" bên trên góc trái giao diện

<img src="https://i.imgur.com/JPancjz.png">

- Chọn mục "NIC"

<img src="https://i.imgur.com/E0637ys.png">

- Chọn "Network source" là Bridge br0 và bấm apply

- Kết quả nhận được là VM sẽ có cùng dải IP với máy chủ KVM
### Cài đặt chế độ NAT cho VM
**Mục đích:** Đặt các VM vào một dải mạng theo chỉ đinh và khi kết nối ra mạng ngoài sẽ sử dụng IP của máy chủ KVM
- Làm các bước tương tự như cài đặt public VM đến tùy chọn "Network source"
- Chọn tùy chọn "Virtual network default : NAT" và bấm apply

<img src="https://i.imgur.com/YjS4LQ3.png	">

- Kết quả nhận được là VM sẽ được cung cấp một IP của dải mạng từ `Virtual network default`. Để xem dải mạng đó là gì, bạn có thể mở file `/etc/libvirt/qemu/networks/default.xml`

<img src="https://i.imgur.com/Cc59KfA.png">

Như vậy, các VM được thiết lập chế độ NAT này sẽ có cùng 1 dải mạng nhưng khi đi ra ngoài Internet sẽ được sử dụng IP của máy chủ KVM.

### Cài đặt chế độ bridge private (isolated mode)
**Mục đích:** Cung cấp một dải mạng riêng cho các VM để có thể giao tiếp được với nhau nhưng không thể kết nối ra bên ngoài
- Từ giao diện chính của virt-manager, tùy chọn `Edit -> Connection details`

<img src="https://i.imgur.com/xHTG209.png">

- Chuyển qua tab "Virtual networking" và tạo một dải mạng riêng cho các VM bằng cách bấm vào icon dấu cộng bên dưới góc trái giao diện này

<img src="https://i.imgur.com/iO8sha2.png">

- Đặt tên cho dải virtual network này

<img src="https://i.imgur.com/uup4WU7.png">

- Giả sử tôi chọn dải mạng 192.168.100.0/24 làm dải mạng riêng cho các máy VM, và điền dải ip cấp phát DHCP

<img src="https://i.imgur.com/xvyjfjk.png">

- Chọn kiểu kết nối tới mạng vật lý. tùy chọn kiểu "isolated" và bấm "finish"

<img src="https://i.imgur.com/pZ4V5Lr.png">




