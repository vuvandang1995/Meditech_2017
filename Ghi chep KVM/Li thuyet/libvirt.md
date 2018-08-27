# Libvirt application development guide using Python
## Giới thiệu
- Libvirt là một bộ công cụ ảo hóa độc lập có thể tương tác với hầu hết các hệ điều hành
## Các khái niệm
1. Hypervisor connections
- Một kết nối là đối tượng chính và cao cấp trong API libvirt và module libvirt. Khởi tạo đối tượng này là bắt buộc trước khi muốn sử dụng các class và methord của nó.
- Một connection được kết nối tới một hypervisor cụ thể. Hypervisor đó có thể nằm trên cùng 1 máy local kiểu như libvirt client hoặc cũng có thể remote trong cùng mạng.
- Trong moi trường hợp, connections được thể hiện bởi lớp `virConnect` và được xác định bởi một URL
- Một ứng dụng cho phép mởi nhiều connections cùng lúc, ngay cả khi nó sử dụng nhiều hơn một loại hypervisor.
- Một khi connect được thiết lập, nó có thể quản lý các đối tượng đã có trước hoặc tạo mới để quản lý (ví dụ như các máy ảo)
2. Guest domains
- Một guest domains có thể tham chiếu tới một máy ảo hoặc cấu hình để có thể khởi động một máy ảo.
- Connections cung cấp các phương thức để liệt kê các máy ảo hoặc tạo mới và quản lý những máy ảo đã có trước.
- Một guest domains được thể hiện bởi lớp `virDomain` và có một số định danh duy nhất như sau:
  - **ID**: là số nguyên dương. Mỗi guest domain có 1 ID duy nhất trên 1 máy chủ. Guest domain không hoạt động thì không có ID
  - **name**: là dạng string, cũng la duy nhất trong tất cả các domain trong host, kể cả domain đang chạy hoặc không hoạt động. Khuyến cáo là nên đặt tên có cả chữ hoa, chữ thường, số, dấu gạch dưới.
  - **UUID**: bao gồm 16 byte kí tự, là duy nhất trong tất cả các máy chủ lưu trữ
- Guest domain chia làm 2 loại: tạm thời và liên tục. Loại tạm thời thì chỉ có thể quản lý nó khi nó đang running, khi nó tắt thì mọi thông tin của nó bị xóa. Còn với loại liên tục thì thông của nó được lưu lại nên khi  nó tắt thì vẫn có thể quản lý được.
3. Virtual networks
- Một mạng ảo cung cấp một phương thức để kết nối tới thiết bị mạng của một hoặc nhiều guest domain trong 1 host.
- Virtual network có thể:
  - Vẫn bị cô lập với mạng host bên ngoài
  - Cho phép định tuyến lưu lượng truy cập off-node thông qua card mạng của máy chủ lưu trữ. Điều đó bao gồm cả các tùy chọn về NAT tới các lưu lượng IPv4
- Một virtual network được thể hiện bởi lớp **virNetwork** và có 2 định danh như sau:
  - **name:** dạng string. là duy nhất trong tất cả các mạng ảo trong host, kể cả những mạng đang chạy hoặc không chạy.
  - **UUID** gồm 16 byte ký tự, là định danh duy nhất trong tất cả các mạng ảo trong tất cả các host. 
- Virtual network cũng chia làm 2 loại: tạm thời và liên tục. Mạng tạm thời thì chỉ quản lý đc khi nó đang chay, khi nó bị tắt thì các thông tin bị mất. Mạng liên tục thì thông tin được lưu lại nên có thể quản lý ngay cả khi nó bị tắt.
- Sau khi cài libvirt, mỗi host có một mạng ảo mặc định trước được gọi là 'default', nó cung cấp dịch vụ DHCP tới các guest và cho phép NAT ra bên ngoài thoogn qua IP của máy chủ thật.
4. Storage pools
- Đối tượng storage pools cung cấp một cơ chế để quản lý các loại storage trong một host, chẳng hạn như: disk, logical volume group, iSCSI target, local/network file system.
- Storage pools được đại diện bởi lớp `virStoragePool`và có 2 định danh là:`name` và `UUID`
5. Storage volumes
- Đối tượng storage volume cung cấp cơ chế quản lý những block storage trong pool như: disk partition, logical volume, hoặc 1 file trong local/network file system
- Sau khi được cấp phát, một volume có thể được sử dụng để cung cấp disk cho 1 hoặc nhiều virtual domain. Nó được thể hiện bởi lớp `virStorageVol` và có 3 định danh như sau:
- **name:** dạng string ngắn, là duy nhất trong tất cả các storage volume trong 1 storage pool. Nó có thể thay đổi khi reboot hoặc chia sẻ giữa các host.
  - **key:** là 1 chuỗi string, dùng để xác định môt volume storage trong storage pool. Nó không bị thay đổi khi reboot hay chia sẻ giữa các host
  - **Path:** là đường dẫn tới volume trong hệ thống. Đường dẫn này là duy nhất trong tất cả các storage volume trong 1 host. 
6. Host devices
- Host devices cung cấp chế độ có thể nhìn thấy các thiết bị phần cứng khả dụng trên máy host. Điều đó bao gồm cả thiết bị USB,m, PCI vật lý và các thiết bị logic như card NIC, disk, disk controller, card âm thanh.
- Host devices được thể hiện bởi lớp `virNodeDev` và có 1 định danh là: **name**
## Driver model

<img src="">

## Remote management
1. Basic usege
2. Data Transports
3. Authentication schemes
4. Generating TLS certificates
5. Public Key Infrastructure setup
## Connections
1. Tổng quan
- Việc đầu tiên mà libvirt agent phải làm là gọi hàm `virlnitializa` hoặc hàm libvirt connection trong Python để tạo 1 đại diện là lớp `virConnect`.
- Module libvirt Python cung cấp 3 cách kháu nhau để connect tới resource:
```
conn = libvirt.open(name)
conn = libvirt.openAuth(uri, auth, flags)
conn = libvirt.openReadOnly(name)
```
- Trong tất cả các trường hợp thì tham số `name` thực tế là **URL** của hypervisor cần connect tới. Các phần trình bày trước đã cung cấp thông tin đầy đủ về các format được chấp nhận của **URL**. Nếu **URL** bằng None thì nó sẽ tự dò tìm tới hypervisor phù hợp và chúng tôi không khuyến cáo điều này. Ứng dụng nên yêu cầu rõ ràng tới các hypervisor cần kết nối bằng cách cung cấp 1 **URL**
1.1 
