## xem các user trong group
ví dụ:
`$ getent group libvirtd`

<img src="https://i.imgur.com/mio2R9i.png">

## Nếu dùng lệnh virt-manager bị lỗi, hay kiểm tra xem khi ssh tới server KVM đã bật xác thực X11 chưa.
Ví dụ: ssh -X user@....

## Để biết VM đang dùng bridge nào (switch ảo) thì vào xem file cấu hình của VM đó 
`/etc/libvirt/qemu/xxx.xml`
## Có thể tạo switch từ linux bridge mà kết nối đươc với card Wifi
link hướng dẫn: https://wiki.debian.org/BridgeNetworkConnections

## Cấu hình VTP trên switch
là để đồng bộ VLAN giữa switch server và switch client. 

## Ping toàn mạng để cập nhật arp table cho KVM từ đó tìm ra IP của các VM thông qua MAC của chúng
- `fping -c 1 -g 192.168.100.0/24`
- `arp -a | grep 52:54:00:cd:76:3a`

Hoặc: sử dụng `nmap -sP 192.168.100.0/24 | grep "52:54:00:CD:76:3A" -B 2` quét toàn bộ mạng sẽ show ra đc IP và MAC của tất cả host trong mạng (tùy chọn `-B 2` là lấy 3 dòng kể từ kết quả lên trên)

<img src="https://i.imgur.com/tTfrdKJ.png">

### Trước khi xóa 1 interface hay bridge với brctl, cần tắt interface, bridge đó trước: 
- VD: 
  `ip link set br0 up`
  `brctl delbr br0`

### Để copy tất cả nội dung của một thư mục sang thư mục khác sử dung `cp -a /directory1/* /directory2`
ví dụ:  cp -a /home/dangvv/kvm-vdi/KVM/hypervisors/* /usr/local/VDI/

### Copy một file từ server hoặc từ local lên server
link: https://www.garron.me/en/articles/scp.html 








# KVM-VDI
- sửa dòng 10 file login.php thành `if ($password==$sql_reply[1]) {`
- sửa ssh_connect thành ssh2_connect (dòng 39 file inc/infrastructure/KVM/UpdateHypervisors.php)
