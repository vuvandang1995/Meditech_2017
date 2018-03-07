# Chuẩn bị
- Máy cài KVM và tạo VN sử dụng hệ điều hành Ubuntu 16.04 và Linux Bridge
## Cài KVM
### B1:
- Kiểm tra xem CPU có hỗ trợ ảo hóa Intel VT-x hoặc AMD-V hay không bằng cách sử dụng câu lệnh `egrep -c '(svm|vmx)' /proc/cpuinfo`

<img src="https://i.imgur.com/SltyUFL.png">

- Nếu kết quả trả về khác 0 là CPU có hỗ trợ ảo hóa. Nếu bạn đang dùng phần mềm ảo hóa VMware thì có thể kích hoạt chức năng này bằng cách vào mục Processor trong cấu hình máy ảo và tích vào mục như hình sau

<img src="https://i.imgur.com/wYiWd97.png">

### B2:
- Cài đặt KVM và các package liên quan

`sudo apt-get install qemu-kvm libvirt-bin libvirt bridge-utils virt-manager`

- Trong đó gói `qemu-kvm` là gói chính để ảo hóa, gói `libvirt-bin` cung cấp tool quản lý qemu và kvm như "virsh"

### B3: Phân quyền
- Chỉ root user và những người trong group `libvirtd` mới có quyền sử dụng và truy cập các VM 
- Khi cài đặt KVM, group `libvirtd` sẽ được tạo ra. Tài khoản đang sử dụng để cài đặt KVM sẽ được tự động thêm vào group này. Điều này cho phép bạn quản lý các máy ảo như một người dùng thường xuyên không phải root. bạn có thể kiểm tra điều này bằng câu lệnh sau

<img src="https://i.imgur.com/Xnx9N29.png">

- Bạn có thể cấp quyền cho một user khác bằng cách add user đó vào group `libvirtd` bằng lệnh

`sudo adduser meditech livirtd`

- Thời điểm này, bạn đã có thể chạy virsh như một người dùng thường xuyên. Để kiểm chứng, hãy thử câu lệnh show các VM sau:

<img src="https://i.imgur.com/GmhaDEy.png">

### B4: Cấu hình Bridge networking
- Để các VM có thể truy cập ra bên ngoài, các VM đó cần thông qua một `bridge ảo` được tạo ra bởi Linux Bridge trên máy chủ KVM. Bridge ảo được kết nối tới card thật và các VM có thể giao tiếp với bên ngoài thông qua bridge ảo đó gọi là Bridge Networking.
- Cài đặt các package của Bridge Linux

`sudo apt-get install bridge-utils`

- Tạo một birdge có tên là br0

`sudo brctl addbr br0`

- Kết nối br0 tới card mạng thật (card mạng thật của tôi ở đây tên là enp1s0)

`sudo brctl addif br0 enp1s0`

- Kiểm tra lại xem br0 đã được kết nối tới enp1s0 chưa

<img src="https://i.imgur.com/An7wE6m.png">

- Cấu hình Bridge networking: Ngắt tất cả các thông số của card thật enp1s0, cấu hình cho br0 sử dụng các thông số của enp1s0 trong file `/etc/network/interfaces` như sau

<img src="https://i.imgur.com/pTvLNbp.png">

- Khởi động lại dịch vụ mạng `sudo /etc/init.d/network-manager restart`

<img src="https://i.imgur.com/BsaP1Nr.png">

### B5: Tạo máy ảo và kiểm tra kết nối
- Tạo máy ảo bằng virt-install <a href="">Tham khảo</a>
- Tạo máy ảo bằng virt-manager <a href="">Tham khảo</a>

Kiểm tra ip trên máy chủ thật

<img src="">

Kiểm tra ip trên VM

<img src="">

Kiểm tra kết nối internet trên VM

<img src="">

