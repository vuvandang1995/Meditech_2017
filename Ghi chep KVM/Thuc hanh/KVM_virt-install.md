# Tạo máy ảo KVM bằng virt-install
- Có 3 cách tạo VM bằng virt-install
	- Taọ VM từ file images
	- Tạo VM từ file ISO đã tải về
	- Tạo VM từ file ISO tải trực tiếp trên Internet
- Tải package để sử dụng virt-install

`sudo apt-get install virtinst`

## 1. Tạo VM từ file images
- Tải file images (giống như file ghost) về thư mục `/var/lib/libvirt/images` <a href="">Tham khảo về file images</a>
	- Di chuyển vào thư mục chứ file images của KVM
	`cd /var/lib/libvirt/images`
	- Tải file images về. Ví dụ
	`wget https://ncu.dl.sourceforge.net/project/gns-3/Qemu%20Appliances/linux-microcore-3.8.2.img`
	- Sử dụng virt-install để tạo VM
	```
	sudo virt-install \
      -n VM01 \
      -r 128 \
       --vcpus 1 \
      --os-variant=generic \
      --disk path=/var/lib/libvirt/images/linux-microcore-3.8.2.img,format=qcow2,bus=virtio,cache=none \
      --network bridge=br0 \
      --hvm --virt-type kvm \
      --vnc --noautoconsole \
      --import
	```
	
<img src="https://i.imgur.com/hmYDfps.png">

Trong đó:
- -n là tên VM
- -r là bộ nhớ RAM
- --vcpu là số CPU
- --os-variant là tùy chọn cho hệ điều hành
- --disk part=... là đường dẫn đến file images
- --network bridge là chọn bridge đã tạo bởi Linux Bridge cho VM
- --hvm: sử dụng đầy đủ tính năng ảo hóa
- --vnc tạo giao diện ảo vnc để sử dụng VM
- --noautoconsole: không tự động kết nối tới guest console

## 2. Tạo VM từ file ISO
- Các bước làm tương tự như file images
- Lệnh tạo VM từ file ISO
```
virt-install --name vmname --ram 1024 --vcpus=1 \
--disk path=/var/lib/libvirt/images/vmname.img,size=20,bus=virtio \
--network bridge=br0 \
--cdrom /home/tannt/ubuntu-14.04.4-server-amd64.iso \
--console pty,target_type=serial --hvm \
--os-variant ubuntutrusty --virt-type=kvm --os-type Linux
```

- Ở đây mình không thêm câu lệnh `--graphics none` để có thể dùng phần mềm đồ họa virt-manager quản lý máy ảo
## 3. Tạo VM từ file ISO tải trực tiếp từ Internet
- Các bước cũng tương tự như file images
- Lệnh tạo VM từ file ISO tải trực tiếp trên Internet
```
virt-install \
--name template \
--ram 1024\
--disk path=/var/kvm/images/template.img,size=20 \
--vcpus 1 \
--os-type linux \
--os-variant ubuntutrusty \
--network bridge=br0 \
--graphics none \
--console pty,target_type=serial \
--location 'http://jp.archive.ubuntu.com/ubuntu/dists/trusty/main/installer-amd64/' \
--extra-args 'console=ttyS0,115200n8 serial'


```