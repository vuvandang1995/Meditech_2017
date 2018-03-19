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
- sửa dòng 10 file login.php thành `if ($password==$sql_reply[1]) {` (vì ngay từ đầu không có tài khoản admin mặc định nên đặt dòng này để insert tài khoản admin1, password: 1 để đăng nhập đã, xong rồi vào add tài khoản admin sau, sau đó trả lại hàm password_verify ban ban đầu của code)

- Chú ý thay thư mục `KVM-VDI` thành `html` hoặc sửa các đường dẫn:
 - `header("Location:  $serviceurl/kvm-vdi/install/");` (file index.php)
 - `header("Location: $serviceurl/kvm-vdi/reload_vm_info.php");` (file login.php)
 - `header("Location: $serviceurl/kvm-vdi/dashboard.php");` (file reload_vm_info.php)
- Sửa file `functions/config.php` các thông số   `$serviceurl`, `$backend_pass`, `$ssh_user`, `mysql_db`, `mysql_user`, `mysql_pass`
- Chú ý cấp quyền đọc thư mục `var/hyper_keys` chứa private key và public key thì ssh được đển server (chmod 705 id_rsa)
- Tạo databases, tạo acc đăng nhập
- Tạo User sử dụng Databases (mysql), cấp quyền cho user đó (lưu ý phải là user đã khai báo trong file config)
- tạo key, copy public key lên server
- Cài popup blocker trên trình duyệt để console vào máy ảo. (https://chrome.google.com/webstore/detail/pop-up-blocker-for-chrome/bkkbcggnhapdmkeljlodobbkopceiche?hl=en)
- Nết bật Spice HTML5 không chạy thì cần vào thư mục `websockify` chạy lại lệnh `./run --token-plugin TokenFile --token-source /tmp/kvm-vdi 5959 --daemon` để bật port và protocol
- cài memcached: `sudo apt-get install -y php-memcached`
- cài curl: `sudo apt-get install php-curl`


## tạo VM bằng virt-install chưa có file .iso (copy file disk vào là dúng đc)
`sudo virt-install --name=centos --disk path=/var/lib/libvirt/images/xxx.qcow2,format=qcow2,bus=virtio,cache=none --disk path=,device=cdrom,target=hdc --soundhw=ac97 --vcpus=1,cores=1,sockets=1 --ram=1 --network bridge=br0,model=virtio --os-type=linux --os-variant=centos7.0 --graphics spice,listen=0.0.0.0 --redirdev usb,type=spicevmc --video qxl --noreboot --import`

## add card vào mncli
https://www.thegeekdiary.com/how-to-configure-and-manage-network-connections-using-nmcli/

## thêm vào file hosts ip và hostname của controller và compute mới cài đc
## nếu có lỗi k truy cập đc mysql trong khi cài openstack
https://www.youtube.com/watch?v=oXjJRrbKjp0

## nếu có lỗi khi Request authentication token cho demo user
lỗi: The request you have made requires authentication. (HTTP 401) (Request-ID: req-1a8b7084-bfdd-4a58-9a38-390f2c29fd87)
sửa:
```
export OS_TENANT_ID=a3dc17aea0b94f9c885fb7bbd8022a52
export OS_TENANT_NAME="demo"
```
trong đó: id và name của project mà bạn muốn add user đó vào
