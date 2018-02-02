## VPN Server Setup 
### Điều kiện tiên quyết : 
- root
- epel-release

`yum –y install epel-release`

- Install openvpn, easy-rsa, iptables : 

`yum –y install openvpn easy-rsa iptables-services`

### 1. Configure easy-rsa :
Phần này phải tạo một số key và certificate
- Certificate Authority (ca)
- Server Key and Certificate
- Diffie-Hellman key (dh)
- Client key and Certificate
### Step 1 - Copy easy-rsa script generation to "/etc/openvpn/".
`cp –r /usr/share/easy-rsa/ /etc/openvpn/`
Sau đó vào /easy-rsa và edit file vars
`cd /etc/openvpn/easy-rsa/2.0/`
`vim vars (Chỉnh sửa thong tin của các export KEY_ )`
`source ./vars (hiện thông báo ./clean-all)`
`./clean-all`

Bây giờ tạo Certificate authority (ca). Điền thông tin được hỏi hoặc ENTER để bỏ qua (thường là sẽ bỏ qua). Dòng lệnh này sẽ tạo ra file ca.crt và ca.key ở thư mục /etc/openvpn/easy-rsa/2.0/keys/
`./build-ca`

### Step 2 - Generate a server key and certificate.
Chạy dòng lệnh build-key-server server  ở thư mục hiện tại :

`./build-key-server server`

(Điền thông tin được hỏi or ENTER để bỏ qua, ENTER ở mục password, chọn [y/n] thì chọn y)
### Step 3 - Build a Diffie-Hellman key exchange.
Chạy lệnh build-dh :

`./build-dh`

### Step 4 - Generate client key and certificate.

`./build-key client`

### Step 5 - Move or copy the directory `keys/` to `/etc/opennvpn`.

`cd /etc/openvpn/easy-rsa/2.0/`
`cp –r keys/ /etc/openvpn/`

### 2. Configure OpenVPN
Có thể copy file mẫu server.conf trong thư mục /usr/share/doc/openvpn-2.*.*/sample/sample-config-file hoặc là tạo mới. Ở đây tôi chọn tạo mới
```
cd /etc/openvpn/easy-rsa/2.0/
vim server.conf
```
```
#change with your port
port 1337
#You can use udp or tcp
proto udp
# "dev tun" will create a routed IP tunnel.
dev tun
#Certificate Configuration
#ca certificate
ca /etc/openvpn/keys/ca.crt
#Server Certificate
cert /etc/openvpn/keys/server.crt
#Server Key and keep this is secret
key /etc/openvpn/keys/server.key
#See the size a dh key in /etc/openvpn/keys/
dh /etc/openvpn/keys/dh2048.pem
#Internal IP will get when already connect
server 192.168.200.0 255.255.255.0
#This line will redirect all traffic through our OpenVPN
push "redirect-gateway def1"
#Provide DNS servers to the client, you can use goolge DNS
push "dhcp-option DNS 8.8.8.8"
push "dhcp-option DNS 8.8.4.4"
#Enable multiple client to connect with same key
duplicate-cn
keepalive 20 60
comp-lzo
persist-key
persist-tun
daemon
#enable log
log-append /var/log/myvpn/openvpn.log
#Log Level
verb 3
```
Lưu lại, tạo 1 thư mục cho log file
`mkdir -p /var/log/myopenvpn/`
`touch /var/log/myopenvpn/openvpn.log`

### 3. Disable firewalld and Selinux
Step 1 - Disable firewalld

`systemctl mask firewalld`
`systemctl stop firewalld`

### Step 2 - Disable SELinux
`vim /etc/sysconfig/selinux`
SELINUX=disabled

Reboot lại máy để nhận những gì đã sửa đổi
### 4. Configure Routing and Iptables
Step 1 - Enable iptables

```
# systemctl enable iptables
# systemctl start iptables
# iptables –F
```

### Step 2 - Add iptables-rule to forward a routing to our openvpn subnet.
iptables -t nat -A POSTROUTING -s ip-tunnel/24 -o eth0 -j MASQUERADE
iptables-save > /etc/sysconfig/iptablesvpn

### Step 3 - Enable port forwarding.
`# vim /etc/sysctl.conf`
thêm vào cuối dòng : 

`net.ipv4.ip_forward = 1`

### Step 4 - Restart network server
`# systemctl start openvpn@server`

## Client Setup
- Cài đặt epel-release
- Cài đặt openvpn
- Copy 3 file ca.crt, client.crt, client.key trên VPN Server vào /etc/openvpn
- Tạo file client.ovpn và chỉnh sửa:

`#vim /etc/openvpn/client.ovpn`

```
client
dev tun
proto udp
#Server IP and Port
remote IP port
resolv-retry infinite
nobind
persist-key
persist-tun
mute-replay-warnings
ca ca.crt
cert client.crt
key client.key
ns-cert-type server
comp-lzo
```

Lưu file và khởi động dịch vụ
`# openvpn /etc/openvpn/client.ovpn`
