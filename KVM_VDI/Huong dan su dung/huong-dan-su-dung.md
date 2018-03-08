# Người quản trị
# Đăng nhập với tư cách người quản trị

<img src="https://i.imgur.com/h5twVG8.png">

# Thêm Hypervisor
- Bấm vào `System` -> `Add hypervisor`

<img src="https://i.imgur.com/qVVZ6eG.png">

- Điền địa chỉ ip của Hypervisor
- Điền port 22 (mặc định là 22)
- Điền tên cho Hypervisor

# Xóa Hypervisor
- Bấm `System` -> `Modify hypervisor`

<img src="https://i.imgur.com/SGfcwht.png">

# Tạo tài khoản admin
- Bấm `System` -> `Add administrator`

<img src="https://i.imgur.com/7N4QX3K.png">

# Xóa tài khoản admin
- Bấm `System` -> `Manage administrator`

# Đổi mật khẩu, đăng xuất tài khoản admin
- Bấm `Profile` -> `Change password`
- Bấm `Profile` -> `Logout`

# Tạo máy ảo

## Tạo `Source machine`
- Bấm `Create Virtual Machines`

<img src="https://i.imgur.com/O4Nsgm0.png">

- Lựa chọn:
	- Machine type: Source machine
	- Target hypervisor: KVM-VDI 138
	- Dung lượng disk
	- Bấm tích vào Mount CD iso: lựa chọn file .iso
	- System info: tùy chọn hệ điều hành
	- Tùy chọn số lượng socket, Cores, dung lượng Ram, bridge
	- Đặt tên máy ảo, số lượng máy ảo cần tạo 
	- Bấm `Create VMs`
## Tạo `Initial machine`
- Bấm `Create Virtual Machines`

<img src="https://i.imgur.com/secgxwO.png">

- Lựa chọn:
	- Machine type: initial machine
	- Target hypervisor: KVM-VDI 138
	- Use volume from: source machine
	- Dung lượng disk
	- System info: tùy chọn hệ điều hành
	- Tùy chọn số lượng socket, Cores, dung lượng Ram, bridge
	- Đặt tên máy ảo, số lượng máy ảo cần tạo 
	- Bấm `Create VMs`

## Tạo `VDI machine`
- Bấm `Create Virtual Machines`

<img src="https://i.imgur.com/oBAOJFl.png">

- Lựa chọn:
	- Machine type:  machine
	- Target hypervisor: KVM-VDI 138
	- Use volume from: initial machine
	- System info: tùy chọn hệ điều hành
	- Tùy chọn số lượng socket, Cores, dung lượng Ram, bridge
	- Đặt tên máy ảo, số lượng máy ảo cần tạo 
	- Bấm `Create VMs`
	
# Tạo tài khoản client
- Bấm `Clients` -> `Add client`


# Xóa tài khoản client
- Bấm `Clients` -> `Manage client`


# Tạo pool
- Bấm `Clients` -> `Add pool`


# Xóa pool
- Bấm `Clients` -> `Manage pool`


# Thêm clients vào pool
- Bấm `Clients` -> `Add clients to pool`


# Thêm VMs vào pool
- Bấm `Clients` -> `Add VMs to pool`

# Quản lý các VDI machine
## Xoá tất cả các VDI machine
- Bấm `VDI control` -> `Delete all child VMs`

## Bật tất cả các VDI machine
- Bấm `VDI control` -> `Mass power on`

## Tắt mềm tất cả các VDI machine
- Bấm `VDI control` -> `Mass shut down (soft)`

## Tắt cứng tất cả các VDI machine
- Bấm `VDI control` -> `Mass shut down (forced)`

## Đưa tất cả VDI machine trở lại trạng thái như Initial machine
- Bấm `VDI control` -> `Populate machines`

## Khóa máy VDI machine (vô hiệu hóa các chức năng trên)
- Bấm `VDI control` -> tích vào `VM locked`

# Snapshot máy ảo
## Tạo snapshot
- Bấm vào `ADD` - điền tên file snapshot

## Quản lý snapshot
- Xóa snapshot: Bấm `SHOW` -> `Delete`
- Rever máy ảo: Bấm `SHOW` -> `Revert`
- Snapshot nào có dấu tích bên cạnh nghĩa là máy ảo đó đang ở trạng thái snapshot đó.
- Bấm vào dấu tích hoặc dấu nhân ở cuối file snapshot để biết thời gian revert snapshot đó gần đây nhất.

## Chỉnh sửa các máy ảo
- Bấm vào tên máy ảo để tùy chọn các thông số chỉnh sửa, xem địa chỉ MAC

- Tham khảo video: https://www.youtube.com/watch?v=WjZTVhHk6ZA

# Người dùng
## Đăng nhập với tư cách người dùng (qua trình duyệt)
- Truy cập địa chỉ Dashboard server, chọn `Go to client area`

<img src="https://i.imgur.com/ZBhEVOK.png">

## Lựa chọn máy ảo để sử dụng.
- Bạn sẽ nhìn thấy tất cả máy ảo trong pool mà bạn thuộc, số lượng thành viên trong pool, số lượng máy trong pool, số lượng máy đang tắt trong pool, xem tình trạng từng máy trong pool (đang bật hay tắt).
- Để truy cập vào máy ảo, chỉ cầm bấm vào máy muốn dùng.

<img src="https://i.imgur.com/fKMiNZy.png">