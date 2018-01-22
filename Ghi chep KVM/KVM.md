# Tìm hiểu KVM
## 1. Khái niệm
- KVM (kernel-based virtual machine) là giải pháp ảo hóa hệ thống linux trên nền tảng phần cứng x86 có các module mở rộng hỗ trợ ảo hóa như Intel VT-x hoặc AMD-V. Về bản chất, KVM thật sự không phải là một hypervisor có chức năng giả lập phần cứng để chạy các máy ảo. Chính xác KVM chỉ là một module của kernel hỗ trợ cơ chế mapping các chỉ dẫn trên CPU ảo (của guest VM) sang chỉ dẫn trên CPU vật lý (của máy chủ chứa VM). Hoặc có thể hình dung KVM giống như một driver của hypervisor để sử dụng được tính năng ảo hóa của các vi xử lý như Intel VT-x hay AMD-V, mục tiêu là tăng hiệu suất cho guest VM
- KVM ban đầu được phát triển bởi Qumranet - một công ty nhỏ, sau đó được Rehat mua lại vào 9/2008. Ta có thể thấy KVM là thế hệ tiếp theo của công nghệ ảo hóa. KVM được sử dụng mặc định từ bản RHEL (Rehat Enterprise Linux) từ phiên bản 5.4 và phiên bản Redhat Enterprise Virtualization dành cho Server.
- Qumranet phát hành code của KVM cho cộng đồng mã nguồn mở. Hiện nay các công ty nổi tiếng như IBM, Intel, ADM cũng đã cộng tác với dự án. Từ phiên bản 2.6.2, KVM trở thành một phần của kernel Linux
- Bảng so sánh KVM với VMware

<img src="http://i.imgur.com/SRDtP40.png">

## 2. Cấu trúc KVMM
- Trong kiến trúc KVM, máy ảo là một tiến trình Linux, được lập lịch chuẩn Linux scheduler. Trong thực tế mỗi CPU ảo xuất hiện như là một tiến trình Linux. Điều này cho phép KVM sử dụng tất cả các tính năng của Linux kernel
- Cấu trúc tổng quan:

<img src="https://i.imgur.com/mriuYGg.png">

- Linux có tất cả cơ chế của một VM cần thiết để chạy các máy ảo. Chính vì vậy các nhà phát triển không xây dựng lại mà chỉ thêm vào đó một vài thành phần để hỗ trợ ảo hóa. KVM được triển khai như một module hạt nhân có thể được nạp vào để mở rộng Linux bởi những tính năng này.
- Trong một môi trường linux thông thường, mỗi process chạy hoặc sử dụng user-mode hoặc kernel-mode. KVM đưa ra một chế độ thứ 3 đó là guest-mode. Nó dựa trên CPU có khả năng ảo hóa với kiến trúc Intel VT hoặc AMD SVM, một process trong guest-mode bao gồm cả kernel-mode và user-mode.
**Cấu trúc tổng quan của KVM bao gồm 3 thành phần chính**
- KVM kernel module
	- Là một phần trong dòng chính của Linux kernel
	- cung cấp giao diện cho Intel VMX và AMD SVM (thành phần hỗ trợ ảo hóa phần cứng)
	- chứa những mô phỏng cho các instructions và CPU modes không được support bởi Intel VMX và AMD SVM
- Qemu-kvm là chương trình dòng lệnh để tạo các máy ảo, thường xuyên được vận chuyển dưới dạng package "kvm" hoặc "qemu-kvm". Có 3 chức năng chính:
	- Thiết lập VM và các thiết bị ra vào (I/O)
	- Thực thi mã khách thông qua KVM kernel module
	- Mô phỏng các thiết bị ra vào (I/O) và di chuyển các guest từ host này sang host khác.
- Libvirt management stack
	- Cung cấp API để các tool như virsh có thể giao tiếp và quản lý VM
	- Cung cấp chế độ quản lý từ xa an toàn.
## 3. Mô hình triển khai KVM
- Hình dưới đâu mô tả mô hình thực hiện của KVM. Đây là một vòng lặp của các hành đông diễn ra để vận hành các máy ảo. Những hành động này được phân cách bằng 3 phương thức chúng ta đã đề cập trước đó: user-mode, kernel-mode, guest-mode.

<img src="https://i.imgur.com/t70nByV.png">

Như ta thấy:
- User-mode: các module KVM gọi đến sử dụng ioclt() để thực thi mã khác cho đến khi hoạt động I/O khởi xướng bởi guest hoặc một sự kiện nào đó bên ngoài xảy ra. Sự kiện này có thể là sự xuất hiện của một gói tin mạng, cũng có thể là trả lời của một gói tin mạng được gửi bởi các máy chủ trước đó. Những sự kiện như vậy được biểu diễn như là tín hiệu dẫn đến sự gián đoạn của thực thi mã khách.
- Kernel-mode: Kernel làm phần cứng thực thi các mã khách tự nhiên. Nếu bộ xử lý thoát khỏi guest do cấp phát bộ nhớ hay I/O hoạt động, kernel thực hiện các nhiệm vụ cần thiết và tiếp tục luồng thực hiện. Nếu các sự kiện bên ngoài như tín hiệu hoặc I/O hoặt động khởi xướng bởi các guest tồn tại, nó thoát tới user-mode.
- Guest-mode: Đây là cấp độ phần cứng, nơi mà các lệnh mở rộng thiết lập của một CPU có khả năng ảo hóa được sử dụng để thực thi mã nguồn gốc, cho đến khi một lệnh được gọi như vậy cần sự hỗ trợ của KVM, một lỗi hoặc một gián đoạn từ bên ngoài.
- Khi một máy ảo chạy, có rất nhiều chuyển đổi giữa các chế độ. Từ kernel-mode tới guest-mode và ngược lại rất nhanh, bởi vì chỉ có mã nguồn gốc được thực hiện trên phần cứng cơ bản. Khi I/O hoạt động diễn ra các luồng thực thi tới user-mode, rất nhiều thiết bị ảo I/O được tạo ra, do rất nhiều I/O thoát và chuyển sang chế độ user-mode chờ. Hãy tưởng tượng mô phỏng một đĩa cứng và 1 guest đang đọc các block từ nó. Sau đó QEMU mô phỏng các hoạt động bằng cách giả lập các hoạt động bằng các mô phỏng hành vi của các ổ cứng và bộ điều khiển nó được kết nối. Để thực hiện các hoạt động đọc, nó đọc các khối tương ứng từ một tập tin lớp và trả về  dữ liệu cho guest. Vì vậy user-mode giả lập I/O có xu hướng xuất hiện một nút cổ chai làm chậm việc thực hiện máy ảo.
## 4. Cơ chế hoạt động
- Để các máy ảo giao tiếp được với nhau, KVM sử dụng Linux Bridge và OpenVswitch. Đây là 2 phần mềm cung cấp các giải pháp ảo hoá network. Trong bài này tôi sử dụng Linux Bridge.
- Linux bridge là một phần mềm được tích hợp vào trong nhân của Linux để giải quyết vấn đề ảo hoá phần network trong các máy vật lý. Về mặt logic, Linux bridge sẽ tạo ra một con switch ảo để cho các VM kết nối vào được và có thể nói chuyện được với nhau cũng như giao tiếp với bên ngoài
- Cấu trức của Linux bridge khi kết hợp cùng KVM-QEMU

<img src="https://i.imgur.com/NA5nOax.png">

Trong đó:
	- Bridge tương đương với switch layer 2
	- Port tương đương với port của switch thật
	- tap (tap interface): card mạng ảo để các VM kết nối với bridge do Linux bridge tạo ra.
	- fd (forward data) chuyển tiếp dữ liệu từ máy ảo tới bridge
- Các tính năng chính của OpenVswitch
	- STP : Spanning Trê Protocol- giao thức chống loop (lặp gói tin trong mạng)
	- VLAN: chia switch ảo (do Linux bridge tạo ra) thành các LAN ảo, cô lập traffic giữa các VM và các VLAN khác nhau cùng một switch
	- FDB (forward database): chuyển tiếp các gói tin theo database để nâng cao hiệu năng của switch. Database lưu các địa chỉ MAC mà nó học được. Khi có gói tin Ethernet đến, Bridge sẽ tìm kiếm trong database có chức địa chỉ MAC không, nếu không nó sẽ broadcast để các port để tìm MAC.
## 5. Tính năng
### Security
- Trong kiến trúc KVM, máy ảo được xem như các tiến trình Linux thông thường, nhờ đó nó tận dụng được mô hình bảo mật của hệ thống Linux như SElinux, cung cấp khả năng cô lập và kiếm soát tài nguyên. Bên cạnh đó còn có SVirt project - dự án cung cấp giải pháp bảo mật MAC (Mandatory Access Control - kiểm soát truy cập bắt buộc) tích hợp với hệ thống ảo hóa sử dụng SElinux để cung cấp một cơ sở hạ tầng cho phép người quản trị định nghĩa nên các chính sách để cô lập các máy ảo. Nghĩa là SVirt sẽ đảm bảo rằng các tài nguyên của máy ảo không thể bị truy cập bởi bất kìa các tiến trình nào khác, việc này cũng có thể thay đổi bởi người quản trị hệ thống để đặt ra quyền hạn đặc biệt, nhóm các máy ảo với nhau chia sẻ chung tài nguyên.
### Memory Management
- KVM thừa kế tính năng quản lý bộ nhớ mạnh mẽ của Linux. Vùng nhớ của máy ảo được lưu trữ trên cùng một vùng nhớ dành cho các tiến trình Linux khác và có thể là swap. KVM hỗ trợ NUMA (Non-Uniform Memory Access- bộ nhớ thiết kế cho hệ thống đa xử lý) cho phép tận dụng hiệu quả vùng nhớ kích thước lớn. KVM hỗ trợ các tính năng ảo của mới nhất từ các nhà cung cấp CPU như EPT (Extended Page Table) của Microsoft, Rapid Virtualzation Indexing (RVI) của AMD để giảm thiểu mức độ sử dụng CPU và cho thông lượng cao hơn. KVM cũng hỗ trợ tính năng Memory page sharing bằng cách sử dụng tính năng của kernel là Kernel Sam-page Mergin (KSM)
### Storage
- KVM có khả năng sử dụng bất kì giải pháp lưu trữ nào hỗ trợ bởi Linux để lưu trữ các Images của các máy ảo, bao gồm các ổ cục bộ như IDE, SCSI và SATA, Network attached Storage (NAS) bao gồm NFS và SAMBA/CIFS hoặc SAN thông qua giao thức iSCSI và Fibre Channel. KVM tận dụng được các hệ thống lưu trữ tin cậy từ các nhà cung cấp hàng đầu trong lĩnh vừa storage. KVM cũng hỗ trợ các images của các máy ảo trên hệ thống tệp tin chia sẻ như GFS2 cho phép các images có thể được chia sẻ giữa nhiều host hoặc chia sẻ chung giữa các ổ logic.
### Live migration
- KVM hỗ trợ live migration cung cấp khả năng di chuyển các máy ảo đang chạy giữa các host vật lý mà không làm gián đoạn dịch vụ. Khả năng live migration là trong suốt với người dùng, các máy ảo vẫn duy trì trạng thái bật, kết nối mạng vẫn đảm bảo và các ứng dụng của người dùng vẫn tiếp tục duy trì trong khi máy ảo được đưa sang một host vật lý mới. KVM cũng cho phép lưu lại trạng thái hiện tại của máy ảo để cho phép lưu trữ và khôi phục trạng thái đó vào lần sử dụng tiếp theo.
### Performance and scalability
- KVM kế thừa hiệu năng và khả năng mở rộng của Linux, hỗ trợ máy ảo với 16 CPUs ảo, 256GB RAM và hệ thống máy host lên tới 256 cores vaf treen 1TB RÁM
