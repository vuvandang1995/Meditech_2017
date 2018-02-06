## LAB cấu hình Bonding theo mode 2 (active-backup)
1. Mô hình
<img src="http://i.imgur.com/ReJmZM4.png">

2. Cài đặt
- Cài gói ifenslave để attach và detach các NIC slave vào đường bond:

```
apt-get install ifenslave

```

- Nạp module bonding vào kernel:
	
```
echo bonding >> /etc/modules
modprobe bonding
```

- Cấu hình bonding network theo mode 2.
	- Cấu hình interface ens3

<img src="https://i.imgur.com/7Bcv5AG.png">

- Cấu hình interface ens6

<img src="https://i.imgur.com/tlpkH6x.png">

- Cấu hình interface bond0

<img src="https://i.imgur.com/FR5uu4g.png">

- Khởi động lại các card mạng

```
ifdown -a && ifup -a
```

- Kiểm tra lại cấu hình bonding: `cat /proc/net/bonding/bond0`

<img src="https://i.imgur.com/RxXF9UQ.png">

- Thử down card ens3 và check log để xem kết quả.

`ifdown ens3`

<img src="https://i.imgur.com/c2xwUFJ.png">

Nhìn log ta thấy khi card ens3 down, thì card bond0 lập tức remove card ens3 và chuyển card ens6 từ trạng thái `standby` sang `active`


## LAB Linux Bridge kết hợp Bonding
1. Mô hình
![](http://i.imgur.com/c9si160.png)


=> **Mục đích: Khi một trong 2 đường mạng eth0 hoặc eth1 bị down, thì máy ảo của khách hàng vẫn có thể kết nối với mạng.**

2. Tạo bond interfaces tên là bond0 kết hợp hai interfaces eth0 và eth1:

```
ifenslave bond0 eth0
ifenslave bond0 eth1
```

**=> Bước này thực chất là bước cấu hình `bond-master bond0` trong file `/etc/network/interfaces`**

3. Tạo switch ảo br0 và gán bond0 interface vào switch đó:

```
brctl addbr br0
brctl addif br0 bond0
```

- Gán hai con ubuntu vào switch br0

![](http://image.prntscr.com/image/3ff0fdb257b24e76aef0d9735d6a6fd8.png)

![](http://image.prntscr.com/image/e70ec59b42294203a7983051dea18ba6.png)

- Kiểm tra lại cấu hình: `brctl show`

```
root@adk:~# brctl show
bridge name	bridge id		STP enabled	interfaces
br0		8000.000c297c7fef	no		bond0
							                vnet0
							                vnet1
```

4. Cấu hình này trong file `/etc/network/interfaces`

```
###############
auto eth0
iface eth0 inet manual
bond-master bond0
bond-primary eth0
################
auto eth1
iface eth1 inet manual
bond-master bond0
################
auto bond0
iface bond0 inet manual
bond-slaves none
bond-mode active-backup
bond-miimon 100
bond-downdelay 200
bond-updelay 200
##############
auto br0
iface br0 inet static
address 10.10.10.195
netmask 255.255.255.0
bridge_ports bond0
bridge_fd 9
bridge_hello 2
bridge_maxage 12
bridge_stp off
```

- Khởi động các card mạng:

```
ifdown -a && ifup -a
```

- Kiểm tra cấu hình bonding: `/proc/net/bonding/bond0`

```
root@adk:~# cat /proc/net/bonding/bond0
Ethernet Channel Bonding Driver: v3.7.1 (April 27, 2011)

Bonding Mode: fault-tolerance (active-backup)
Primary Slave: eth0 (primary_reselect always)
Currently Active Slave: eth0
MII Status: up
MII Polling Interval (ms): 100
Up Delay (ms): 200
Down Delay (ms): 200

Slave Interface: eth1
MII Status: up
Speed: 1000 Mbps
Duplex: full
Link Failure Count: 0
Permanent HW addr: 00:0c:29:7c:7f:f9
Slave queue ID: 0

Slave Interface: eth0
MII Status: up
Speed: 1000 Mbps
Duplex: full
Link Failure Count: 0
Permanent HW addr: 00:0c:29:7c:7f:ef
Slave queue ID: 0
```