# Overview
- OpenStack là một nền tảng điện toán đám mây mới mục đích đơn giản hóa việc thực hiện, khả năng mở rộng cao và tính năng phong phú
- Tính đến thời điểm hiện tại là bạn Release OpenStack thứ 15, có tới 1925 developer trên toàn thế giới tham gia đóng góp cho dự án này.

**Note: bài lab này sử dụng để tìm hiểu OpenStack. Mô hình product sẽ phức tạp hơn.**

- Sau khi làm quen với cài đặt cơ bản, cấu hình, xử lý sự cố các dịch vụ của OpenStack, các bước để triển khai product:
	- Xác định và implement các dịch vụ cốt lõi và các tùy chọn cần thiết để đáp ứng hiệu năng và dự phòng
	- Tăng cường an ninh bằng các phương pháp như firewall, mã hóa, các dịch vụ policy
	- Sử dụng các công cụ như Ansialbe, Chef, Puppet, Salt để tự động triển khai và quản lý product.
# Example architecture
- Mô hình lab này cần tối thiểu 2 node(host) để chạy một virtual machine hoặc instance
- Các service tùy chọn như Block Storage và Object Storage thì cần thêm các node bổ sung
- Mô hình này là mô hình tối thiểu khác với mô hình triển khai product:
	- Các networking agent được cài ngay trên Node Controller thay vì trên một node Network riêng biệt
	- Network dành cho trao đổi lưu lượng giữa các máy trong self.service networks sử dụng managerment network thay vì tách thành 1 đường mạng riêng.
- Yêu cầu phần cứng:

<img src="">

Trong đó:
## Controller
- Node Controller chạy Identiny service, Image service, quản lý Compute, quản lý Networking, Networking agent và Dashboard. Nó cũng bao gồm hỗ trợ các dịch vụ như SQL databases, message quêu, NTP
- Tùy chọn, node Controller có thể chạy Block Storage, Object Storage, Orchestration, Telematry
- Node Controller cần ít nhất 2 Network interface.
## Compute
- Node Compute chạy phần hypervisor của Compute để điều khiển instance (VM). Mặc định Compute sử dụng KVM hypervisor
- Node Compute cũng chạy một agent Networking service, nó sẽ connect đến các instance để ảo hóa network và cung cấp firewall cho các service
- Có thể deploy nhiều hơn 1 node Compute. Mỗi node cần tối thiểu 2 network interface.
## Block Storage
- Node tùy chọn: Block Storage chứa các disk sử dụng các dịch vụ Block Storage và Share File System để cung cấp cho instance.
- Để đơng giản, traffic của service giữa các node compute và node này sử dụng đường mạng management. Còn trên môi trường product, nên triển khai một đường mạng riêng để tăng hiệu quả và bảo mật
- Có thể deploy nhiều hơn 1 node Block Storage. Mỗi node tối thiểu 2 network interface.
## Object Storage
- Node tùy chọn: Object Storage chứa các disk sử dụng các dịch vụ Object Storage để sử dụng cho việc lưu trữ các account, container và object
- Để đơn giản, traffic của service giữa các node Compute và node này sử dụng chung đường network management. Còn trên môi trường product, nên triển khai một đường mạng riêng để tăng hiệu quả và bảo mật.
- Có thể deploy nhiều hơn một node Object Storage. Mỗi node tối thiểu 2 network interface
- Service này cần 2 node, mỗi node tối thiểu 2 network interface, có thể deploy nhiều hơn 2 node
## Networking
- Có 2 mô hình networking
	- Provider network
	- Self.service network
### Provider network
- Các service chủ yếu là layer 2 (bridging/switching) và VLAN. Về cơ bản, nó làm cầu nối cho các mạng ảo với mạng vật lý trên cơ sở hạ tầng mạng vật lý cho các service layer 3.
**Warning: Option này không hỗ trợ mạng self.service (private), layer 3 (routing) serviec và các service nâng cao như LBasaS, FWaas**

<img src="">

### Self.Service network
- Mô hình này cung cấp các service layer 3. Về cơ bản, nó sẽ định tuyến các mạng ảo tới các mạng vật lý sử dụng NAT.
- Ngoài ra, tùy chọn này còn cung cấp nền tảng cho các dịch vụ như LBaaS, FWaaS.

<img src="">

