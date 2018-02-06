# Lab kết nối 2 namespace
- Có rất nhiều cách để kết nối 2 namespace

## Cách 1:
- Là cách đơn giản nhất để kết nối 2 namespace, sử dụng `veth pair`
### veth là gì?
- **Virtual Ethernet interfaces** (hay veth) là một kiến trúc thú vị, chúng luôn có 1 cặp, và được sử dụng để kết nối như một đường ống: các lưu lượng tới từ một đầu veth và được đưa ra, peer tới giao diện veth còn lại. Như vậy, có thể dùng veth để kết nối mạng trong namespace từ trong ra ngoài root namespace trên các interface vật lý của root namespace.
### Các bước thực hiện
- B1: tạo các namespace

```
ip netns add ns1
ip netns add ns2
```

<img src="https://i.imgur.com/ITWoQ0z.png">

- B2: Tạo veth pair

`ip link add tap1 type veth peer name tap2`

- B3: Gắn interface vừa tạo vào các namespaces

```
ip link set tap1 netns ns1
ip link set tap2 netns ns2
```

- B3: bật các interface đó lên

```
ip netns exec ns1 ip link set dev tap1 up
ip netns exec ns2 ip link set dev tap2 up
```

### Kiểm tra
- Xem các interface của từng namespaces, sử dụng lệnh:

`ip netns exec <namespace> ip a`

<img src="https://i.imgur.com/Gj5Jxbh.png">

- Gán địa chỉ ip cho từng namespaces

<img src="https://i.imgur.com/YltILtO.png">

- Ping thử 2 namespaces

<img src="https://i.imgur.com/UKHGXYN.png">

## Cách 2:
- Kết nối 2 namespace sử dung Linux bridge và veth pair
- Mô hình

<img src="https://i.imgur.com/Ivwq9Hy.png">

- Khi có nhiều hơn 2 network namespace (hoặc KVM) mà cần kết nối chúng với nhau, Linux brigde là một sự lựa chọn khá tốt.
### Các bước thực hiện
- B1: tạo các namespace

```
ip netns add ns1
ip netns add ns2
```

<img src="https://i.imgur.com/ITWoQ0z.png">

- B2: tạo bridge

```
BRIDGE=br0
brctl addbr $BRIDGE
brctl stp $BRIDGE off
```

- B3: tạo port pair

```
ip link add tap1 type veth peer name br-tap1
ip link add tap2 type veth peer name br-tap2
```

- B4: Gán port cho bridge br0

```
brctl addif br0 br-tap1
brctl addif br0 br-tap2
```

- B5: Gán port còn lại cho các namespaces

```
ip link set tap1 netns ns1
ip link set tap2 netns ns2
```

- B6: bật tất cả các port bên trên

```
ip link set dev br0 up
ip link set dev br-tap1 up
ip link set dev br-tap2 up
ip netns exec ns1 ip link set dev tap1 up
ip netns exec ns2 ip link set dev tap2 up
```

### Kiểm tra
- Xem các interface của từng namespaces, sử dụng lệnh:

`ip netns exec <namespace> ip a`

<img src="https://i.imgur.com/Gj5Jxbh.png">

- Gán địa chỉ ip cho từng namespaces

<img src="https://i.imgur.com/YltILtO.png">

- Ping thử 2 namespaces

<img src="https://i.imgur.com/UKHGXYN.png">

## Cách 3:
- Kết nối 2 namespaces sử dụng OpenvSwitch và verh pair

<img src="https://i.imgur.com/yC3LeCk.png">

## Cách 4:
- Kết nối 2 namespace sử dụng OpenvSwich port

<img src="https://i.imgur.com/xC5zuvl.png">

## Cấu hình cho các namespaces kết nối ra ngoài internet sử dụng Linux bridge
- B1: Gán bridge `br0` với card mạng thật của máy thật

`brctl addif br0 ens3`

Ví dụ ở đây tên card thật máy của tôi là `ens3`

- B2: Cấu hình cho `ens3` trong file `/etc/network/interfaces` như sau:

```
auto ens3
iface ens3 inet manual
```

- B3: Cấu hình cho `br0` trong file `/etc/network/interfaces` như sau:

**Lưu ý:** Nếu cấu hình cho `br0` nhận ip static ngay, hệ thống sẽ không nhận ra được, vậy nên trước khi đặt ip stactic cho `br0`, bạn nên đặt ip dhcp cho nó trước
** Cấu hình dhcp cho br0

```
auto br0
iface br0 inet dhcp
        bridge_ports ens3
        bridge_stp off
        bridge_fd 0
        bridge_maxwait 0
```

** Cấu hình ip static cho br0

```
auto br0
iface br0 inet static
        address 192.168.100.134
        netmask 255.255.255.255
        network 192.168.100.0
        broadcast 192.168.100.255
        gateway 192.168.100.1
        dns-nameservers 8.8.8.8
        bridge_ports ens3
        bridge_stp off
        bridge_fd 0
        bridge_maxwait 0
```

- Xóa các thông số của card thật

`ip addr flush ens3`

- B4: khởi động lại network

`/etc/init.d/networking restart`
`ifdown -a && ifup -a`

- B5: gán ip cho các namespaces cùng dải mạng với server

<img src="https://i.imgur.com/JSDZ8a6.png">

- B6: Gán ip route default cho các namespaces

<img src="https://i.imgur.com/fdxEQ2j.png">

Bật chế độ `IPv4 forwarding` trên server

`echo 1 > /proc/sys/net/ipv4/ip_forward`

**Lưu ý:** tắt iptables của server đi hoặc cấu hình cho các namespaces có thể ra ngoài mạng.

- B7: Kiểm tra ping thử từ namespaces ra internet

<img src="https://i.imgur.com/L93GD8z.png">