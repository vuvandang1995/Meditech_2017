# OPS-VDI
## Giới thiệu
### 1. Ý tưởng thực hiện
- Là hệ thống ứng dụng web triển khai theo mô hình Admin-User
- Tích hợp mô hình VDI.
### 2. Mục đích
- Tạo ra một công cụ thuận tiện cho việc quản lý các máy ảo nhiều cụm OpenStack trên cùng một Dashboard.
- Quản lý và phân quyền cho User sử dụng các máy ảo theo group, cá nhân.
### 3. Các ý tưởng tính năng dự kiến
  - **Admin**: Là người trực tiếp giám sát, quản lý hệ thống.
    - Add các cụm OpenStack vào hệ thống
    - Hiển thị thông tin các máy ảo (đã có trước đó hoặc tạo mới) trên các cụm OpenStack sau khi add và hệ thống: Name, image, ip, status, network, ...
    - Các thao tác máy ảo cơ bản như: Thêm, sửa, xóa, console, stop, pause, start, snapshot, migrate, ...
    - Khi tạo máy ảo, các thông số như RAM, VCPUS, DISK có thể tự định nghĩa, hệ thống sẽ kiểm tra cấu hình đó đã tồn tại hay chưa, nếu có rồi sẽ tạo máy ảo luôn, chưa có sẽ tạo mới.
    - Tạo nhiều máy ảo cùng lúc theo cơ chế clone.
    - Chỉnh sửa cấu hình máy ảo
    - Tạo tài khỏan User, tạo group và add User vào các group
    - Add máy ảo vào Group, điều đó có nghĩa là các User sẽ có quyền sử dụng (start, stop, console, snapshot ...) các máy ảo được add vào group đó.
    - Giám sát quản lý các group: xem có bao nhiêu máy ảo, trạng thái (running or stop, paused,..), User nào đang sử dụng, monitor các thông số như ram, cpu, disk các máy ảo đó, xóa all, ...
    - Trao đổi với User qua tab chat.
    
  - **User**: Là người sử dụng các máy ảo được cấp
    - Quản lý thông tin các máy ảo được Admin cấp: Name, image, ip, status, network, ...
    - Các thao tác máy ảo cơ bản như: Thêm, sửa, xóa, console, stop, pause, start, snapshot, ...
    - Monitor thông các máy ảo được cấp.
    - Trao đổi với Admin qua tab chat.
    - Gửi ticket thông báo tới hệ thống và email cho Admin khi cần hỗ trợ.
    - Share màn hình cho Admin nếu cần thiết.
### 4. Các công nghệ dự kiến sử dụng
- Framework Django
- Jquery, Javascript
- Websocket (channel-django)
- Web-RTC
- OpenStack API
- Hệ thống Cobbler để chạy kịch bản tự động cài hệ điều hành khi tạo máy ảo bằng image boot từ file .iso (mong muốn)
- Hệ thống giám sát zabbix để monitor được nhiều thông số hơn, tốt hơn. (mong muốn)
