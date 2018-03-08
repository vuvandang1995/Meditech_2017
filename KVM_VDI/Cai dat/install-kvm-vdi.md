# Cài đặt
## Dashboard
- Trên Ubuntu 16

`apt-get install mariadb-server apache2 php git libapache2-mod-php php-mbstring php-gettext php-ssh2 php-imagick php-mysql php-mail`

- Trên Ubuntu 15

`apt-get install mariadb-server apache2 php5 git libapache2-mod-php5 php-gettext php5-ssh2 php5-imagick php5-mysql php5-mail`

- Tải code về Dashboard

`git clone https://github.com/vuvandang1995/Meditech_2017.git`

- Copy file `/Meditech_2017/html_chuan` vào thư mục `/var/www` với tên thư mục là `html`:

`sudo cp -a /home/dangvv/Meditech_2017/html_chuan /var/www/html`

- Vào thư mục `/var/www/html`, Chỉnh sửa file `functions/config.php` các thông số: $serviceurl, $backend_pass, $ssh_user, $mysql_db, $mysql_user, $mysql_pass. trong đó:
	- `$serviceurl`: là điạ chỉ của **Dashboard** server
	- `$backend_pass`: là mật khẩu để xác thực với **Hypervisor** (trên Hypervisor cũng phải cấu hình mật khẩu giống $backend_pass trong file config)
	- `$ssh_user`: là user name để **Dashboard** ssh vào **Hypervisor**
	- `$mysql_db`: là tên database trên **Dashboard**
	- `$mysql_user`: tên user có thể truy cập vào $mysql_db
	- `$mysql_pass`: là mật khẩu của $mysql_user
- Kiểm tra: Truy cập vào địa chỉ của **Dashboard**, nếu mọi thứ ổn, bạn sẽ thấy 1 giao diện web đăng nhập như sau

<img src="https://i.imgur.com/PsVbLau.png">

## Hypervisor
- KVM_VDI sử dung python 2. bạn có thể gặp một số vấn đề nếu sử dụng python 3

`apt-get install qemu-kvm libvirt-bin sudo python python-requests virtinst socat libxml-xpath-perl`

***Lưu ý:*** Ubuntu apparmor!

- Bạn cần phải tắt tính năng apparmor trên Hypervisor. Nếu không tắt tính năng này, bạn sẽ không thể khợi động máy ảo

```
service apparmor stop
update-rc.d -f apparmor remove
apt-get remove apparmor apparmor-utils
reboot
```

- Trên **Dashboard server** và **Hypervisor server**, bạn cần tạo một user tên VDI

`useradd -s /bin/bash -m VDI`

- Trên **Dashboard server** tạo ssh key với chế độ User VDI

```
su VDI
cd
ssh-keygen -t rsa
```

- Trên **Dashboard server**
	- Tạo thư mục `/var/hyper_keys`
	- Copy file private key và public key từ thư mục `/home/VDI/.ssh` đến thư mục `/var/hyper_keys`
	- Copy file public key từ thư mục `/var/hyper_keys` tới file `/home/VDI/.ssh/authorized_keys` trên **Hypervisor**
	- Cấp quyền đọc cac file trong thư mục `/var/hyper_keys`: `sudo chmod 705 id_rsa` `sudo chmod 705 id_rsa.pub`
- Kiểm tra: hãy thử lệnh

`ssh -i /var/hyper_keys/id_rsa VDI@hypervisor_address`

- Tạo database trên **Dashboard server**
	- Truy cập vào mysql bằng lệnh: `mysql -u root -p password`
	- Tạo user sử dụng mysql: `CREATE USER 'VDI'@'localhost' IDENTIFIED BY '12356';` (đây là user đã được khai báo trong file `functions/config.php` với thống số $mysql_user)
	- Cấp quyền cho user `VDI`: `GRANT ALL PRIVILEGES ON * . * TO 'VDI'@'localhost';`
	- Tạo database với tên `vdi`: `CREATE DATABASE vdi;` (đây là user đã được khai báo trong file `functions/config.php` với thống số $mysql_db)
	- Tạo các table cần thiết cho database:
	```
	cd /var/www/html/sql/
	mysql -u VDI -p vdi < create.sql
	```

	
- Nếu ssh thành công không cần mật khẩu, bạn đã làm đúng.

- Trên **Hypervisor server**:
	- Tạo thư mục `/usr/local/VDI`
	- Copy tất cả file từ thư mục `/home/dangvv/Meditech_2017/html_chuan/KVM/hypervisors/` vào thư mục: `/usr/local/VDI/`
	- Chỉnh sửa file `/usr/local/VDI/config` các thông số address(địa chỉ của Dashboard server), password. **Lưu ý: password ở file này cần giống với thông số $backend_pass trong file `functions/config.php` trên Dashboard server **
	- Thêm dòng `VDI     ALL=(ALL:ALL) NOPASSWD:ALL` vào file `/etc/sudoers`
	- Copy file `/usr/local/VDI/vdi-agent.service` vào `/etc/systemd/system`
	- Reload lại systemd: `systemctl daemon-reload`
	- Enable vdi-agent: `systemctl enable vdi-agent`
	- Start vdi-agent: `systemctl start vdi-agent`
	- Kiểm tra status của vdi-agent: `systemctl status vdi-agent`

### Thin client
- Cài các gói sau:

`apt-get install python python-requests virt-viewer python-qt4 python-webkit python-urllib3 python-gtk2`

- Tạo thư mục: `/usr/local/VDI-client/`
- Clone thư mục: `git clone https://github.com/vuvandang1995/Meditech_2017.git`
- Copy tất cả file từ thư mục `/Meditech_2017/html_chuan/thin_clients` vào thự mục `/usr/local/VDI-client/`
- Chỉnh sửa file `/usr/local/VDI-client/config` với thông số address (địa chỉ của Dashboard server)

### HTML5 SPICE client
- KVM-VDI sử dụng HTML5 SPICE để có thể truy cập vào các máy ảo. Để làm việc này, bạn cần có **websockify server** chạy trên **Dashboard server**
- Chạy các lệnh sau trên Dashboard server:

```
git clone https://github.com/kanaka/websockify
cd websockify
./run --token-plugin TokenFile --token-source /tmp/kvm-vdi 5959 --daemon
```

- Nếu Dashboard server sử dụng `https` thì chạy lệnh: `./run --token-plugin TokenFile --token-source /tmp/kvm-vdi 5959 --cert=CERTFILE --key=KEYFILE --daemon`
- Bạn cần chỉnh sửa file `/var/www/html/spice_html5/run.js` tại dòng có `'protocol': getURLParameter('protocol') || 'wss',`, giữ nguyên nếu bạn sử dụng https, sửa thành `ws` nếu bạn dùng http
