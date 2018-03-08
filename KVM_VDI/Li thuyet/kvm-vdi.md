# Giới thiệu
## 1. Khái niệm
- **KVM-VDI** là mô hình giải pháp [VDI](http://viettelis.com/vdi) sử dụng công nghệ ảo hóa KVM.
- Project này cung cấp đầy đủ chức năng giải pháp VDI với công nghệ ảo hóa mã nguồn mở. Nó cung cấp 2 kiểu ảo hóa backend: QEMU-KVM và OpenStackà.

<img src="https://scontent.fhan3-2.fna.fbcdn.net/v/t1.0-9/28782591_1895515330520453_4087624129128169472_n.png?oh=205545d712c810910a117009936ba5de&oe=5B449D30">

### KVM-VDI KVM backend
- Mục đích của mô-đun này là cung cấp đầy đủ chức năng giải pháp VDI sử dụng **QEMU-KVM hypervisor**.
- Mô-đun này gồm 3 phần:
	- **Dashboard:** Là một dịch vụ web, cung cấp giao diện điều khiển các máy ảo.
	- **Thin client:** Là chương trình script, cung cấp chức năng truy cập vào các máy ảo từ phía người dùng.
	- **Hypervisor:** Là server, là tài nguyên để tạo ra các máy ảo cho người dùng sử dụng.
- Projec này sử dụng **QEMU-KVM** và cung cấp máy ảo cho **Thin client** thông qua giao thức SPICE. 
- Yêu cầu từng thành phần:
	- **Thin client** được khởi động và sử dụng máy ảo trong mạng bằng 2 cách.
		- Mở ứng dung python trong thư mục `/usr/local/VDI-client`, kết nối qua **remote viewer**
		- Kêt nối tới máy ảo thông qua trình duyệt (có giao diện người dùng).
	- **Hypervisor** phải tạo một tài khoản người dùng dùng quyền *sudo* không cần password. **Hypervisor** được cài sẵn KVM.
	- **Dashboard** là dịch vụ web được ssh tới **Hypervisor** với private key và public key.
### Dashboard service
- **Dashboard** có 4 kiểu máy ảo:
	- **Simple machine:** là máy ảo chuẩn, sử dụng đơn thuần, không thuộc dịch vụ VDI.
	- **Source machine:** là máy ảo chuẩn, **Initial machine** sẽ lấy máy này làm cơ sở để tạo ra các **VDI machine** với phương thức clone.
	- **Initial machine:** là máy ảo chuẩn được copy drive image của **Source machine**. Là máy ảo cơ sở tạo ra  và quản lý các **VDI machine** (thêm, xóa, bảo trì, snapshot hàng loạt,...).
	- **VDI machine:** là máy ảo chuẩn, được quản lí và tạo ra bởi  **Initial machine** qua phương thức clone, cung cấp cho người dùng.
- **Dashboard** cung cấp một mô trường VDI, bạn nên thực hiện theo các bước sau: tạo một **Source machine** và cài đặt hệ điều hành cho nó. Tiếp theo tạo **Initial machine**, nó sẽ tự động copy file disk từ **Source machine**. Tạo **VDI machine** với số lương theo yêu cầu từ **Initial machine**.
### VDI-agent
- VDI-agent là một dịch vụ chạy trên mỗi **Hypervisor**. Nó chấp nhận các lệnh với định dạng json thông qua UNIX socket từ **Dashboard service**.
- Hiện tai, nó cung cấp các chức năng sau:
	- VDI-agent khởi động máy ảo:
		- Ví dụ một thông điệp chính xác json tới VDI-agent để khởi động VM :

		- `{"command": "STARTVM", "vmname": "VMnaname", "username": "UserName", "password": "Password", "os_type": "windows(or linux)"}`

		- Bạn có thể tìm hiểu thêm thông tin về VDI-agent [tại đây](https://www.neblogas.lt/2016/07/18/technical-info-ovirt-agent-sso/)
	- **Disk image copy:** VDI-agent sử dụng lệnh `qemu-img convert` copy file image từ **Source machine** sang **Initial machine** 
		- Ví dụ một thông điệp chính xác json tới VDI-agent để copy disk :
		- `{"command": "COPYDISK", "vm": "VMid", "source_file": "SourceFile", "destination_file": "DestinationFile"}`
## Hoạt động
- **Dashboard** ssh vào các **Hypervisor** thông qua giao thức SSH(sử dụng key).ào
- **Dashboard** tạo các máy ảo trên **Hypervisor** để cung cấp cho người dùng sử dụng
- Người dùng truy cập vào các máy ảo thông qua trình duyệt hoặc ứng dụng cho trước.
## Các chức năng
- Từ giao diện của **Dashboard**, người quản trị có thể :
	- Thêm, xóa, khởi động, tắt, snapshot, bật chế độ bảo trì, console vào các máy ảo.
	- Chia địa chỉ IP cho các máy ảo trong 1 dải IP cho trước.
	- Quản lý(thêm, xóa, tạo mới mật khẩu) tài khoản cho mỗi người dùng, phân quyền chỉ định máy ảo cụ thể người dùng được phép sử dụng.
	- Chia người dùng và các máy ảo thành các pool để quản lý.
	- Chỉnh sửa lại thông tin các máy ảo sau khi tạo. (OS type, Machine type, Use volume from)
- Thông qua trình duyệt, hoặc ứng dụng cho trước, người dùng có thể:
	- Chủ động lựa chọn máy ảo có sẵn trong pool của hộ để khởi động, tắt, truy cập. 
## Ưu, nhược điểm của mô hình KVM-VDI
### Ưu điểm:
- Giao diện quản lý cho admin khá dễ dùng, gam màu nhẹ nhàng.
- Có thể tạo đồng thời nhiều máy ảo.
- Cơ chế xác thực cho người dùng. (mỗi người dùng một tài khoản, chỉ sử dụng được những máy ảo cho phép).
- Phân quyền chỉ định máy ảo cụ thể người dùng được phép sử dụng bằng cách tạo các pool.
- Mô hình KVM-VDI bảo mật (không dùng internet)
- Quản lý tập trung các máy ảo( tắt, bật, khôi phục ban đầu, xóa đồng thời các máy ảo thuộc VDI control)
- Bật chế độ bảo trì cho VDI machine (người dùng sẽ không thể sử dụng máy ảo nào được bật chế độ bảo trì)
- Người quản trị có thể theo dõi được tình trạng các máy ảo (sử dụng hệ điều hành nào, đang bật hay tắt, ai đang sử dụng)
- Người dùng có thể theo dõi được tình trạng các máy ảo họ được sử dụng (đang bật hay tắt) để chủ động lựa chọn.
- Người quản trị có thể snapshot từng máy ảo (tạo được nhiều file snapshot, revert, biết được tình trạng máy ảo đang ở trạng thái snapshot nào, thời gian revert trạng thái snapshot đó)
- Người dùng có thể sử dụng đồng thời nhiều máy (hiện tại chỉ làm được điều này thông qua trình duyệt)
- Một **Dashboard** có thể kết nối tới nhiều **Hypervisor**.
### Nhược điểm
- Chưa có chức năng chỉnh sửa cấu hình cho các máy ảo