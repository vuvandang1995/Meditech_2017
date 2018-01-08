LVM snapshots là một tính năng cho phép tạo ra các bản sao lưu dữ liệu cho Logical Volume, thêm nữa, nó còn cung cấp một tính năng phục hồi dữ liệu cho Logical Volume.
Nó hoạt động với LVM và chỉ mất thêm không gian lưu trữ cho những sự thay đổi của Logical volume gốc. Sẽ là tốt nhất khi những sự thay đổi luôn nhỏ hơn không gian cho phép của Snapshot.
<img src="">

Nếu Snapshot hết dung lượng, chúng ta có thể mở rộng bằng lệnh `lvextend`, và ngược lại, để giảm dung lượng snapshot thì dùng lệnh `lvreduce`
Nếu bạn lỡ tay xóa bất kì file nào trong Logical volume gốc sau khi đã tạo snapshot cho nó thì cũng không phải lo lắng vì đã có snapshot.

Các bước tạo và sử dụng snapshot trong LVM
B1: Tạo LVM snapshot
Đầu tiên, cần kiểm tra dung lượng trống trong vulume group để tạo snapshot. Sử dụng lệnh `vgs`
<img src="">

Như thông tin đã thấy, dung lượng trống là 8GB. Vì thế tạo một snapshot cho logical volume có tên là `tecmint_datas`. Tạo snapshot dung lượng 1GB với lệnh như sau:
<img src="">

Trong đó:
1: dung lượng snapshot
2: tùy chọn tạo snapshot
3: tùy chọn tên snapshot
4: tên snapshot
5: Volume cần tạo snapshot

Nếu bạn muốn xóa snapshot thì sử dụng lệnh `lvremove`
<img src="">

Kiểm tra lại snapshot đã tạo bằng lệnh `lvs`
<img src="">

Như bạn thấy thì một snapshot đã được tạo thành công.
<img src="">

Giả sử bây giờ thêm một file mới có dung lương 650MB vào lv nguồn `/tecmint_datas`. Mà snapshot được tạo ra có dung 
lượng là 1GB. Vậy chúng ta kiểm tra lại xem có đúng snapshot đã sử dụng 650MB để lưu trữ file mới kia không.
<img src="">

Để xem thông tin chi tiết của snapshot đó, sử dụng lệnh `lvdisplay`
<img src="">

Trong đó:
1. Tên snapshot
2. Nhóm tạo snapshot
3. Các chế độ đọc/ghi của snapshot
4. Thời gian snapshot được tạo (thông tin này quan trọng vì mọi sự thay đổi sẽ được lưu sau thời gian này)
5. Trạng thái của snapshot (đang hoạt động cho lv nguồn tên là tecmint_datas)
6. Trạng thái của lv (có thể mở rộng snapshot hay không)
7. Dung lượng của lv nguồn
8. Dung lượng lưu trữ những thay đổi của nguồn
9. Phần trăm dung lương snapshot đã sử dụng
10.

Một tình huống khác là khi thêm 1 file có dung lượng lớn hơn 1GB vào lv nguồn, bạn sẽ thấy một lỗi `Input/output error`
có nghĩa là lỗi vượt quá dung lượng snapshot
<img src="">

Đó là lí do chúng ta nên tạo snapshot có dung lượng tối thiểu bằng dung lượng lv nguồn.

B2: Mở rộng Snapshot trong LVM
Để mở rộng dung lượng cho snapshot trước khi nó bị đầy bằng cách sử dụng lệnh `lvextend`
<img src="">

Kiểm tra sự thay đổi bằng lệnh `lsdisplay`
<img src="">

B3: Khôi phục dữ liệu bằng snapshot
Để khôi phục dữ liệu, đầu tiên bận cần unmount filesystem cần khôi phục
<img src="">

Kiểm tra lại xem filesystem đó đã được unmount chưa:
<img src="">

Tiếp theo sử dụng lệnh `lvconvert` để convert từ snapshot vào lv cần khôi phục
<img src="">

Sau khi tiến trình kết thúc, kiểm tra lại kết quả với lệnh `df -Th`
<img src="">

Đồng thời, snapshot cũng bị tự động xóa sau khi khôi phục cho lv gốc
<img src="">

**Chú ý quan trọng:** Để mở rộng snapshot một cách tự động, chúng ta có thể chỉnh sửa trong file cấu hình. Ví dụ như sau:
Mở file cấu hình bằng trình soạn thảo:

`# vim /etc/lvm/lvm.conf`

Tìm kiếm đến dòng có `autoextend`. Các giá trị mặc định là 100 và 20 như hình bên dưới
<img src="">
