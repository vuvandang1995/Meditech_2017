- Hiểu được hệ thống hoạt động như thế nào là cách tốt nhất để đối phó với những sự cố.
# Analyzing the Linux boot process

## The beginning of boot: the OFF state
- Wake-on-LAN
	- Trạng thái OFF có nghĩa là hệ thống không có điện, phải không? Rõ ràng điều đó là không phải. Ví dụ, đèn LED của Ethernet sáng vì bật wake-on-LAN (WOL) trên hệ thống của bạn. Hãy kiểm tra bằng cách sau:

`$# sudo ethtool <interface name>`

<img src="https://i.imgur.com/UijbHhJ.png">

- Thẻ <interface name> ở đây có thể ví dụ là eth0. Nếu bạn nhìn thấy kết quả của trường "Wake-on" là `g`, điều đó có nghĩa là các máy từ xa có thể khởi động hệ thống bằng cách gửi một MagicPacket. Nếu bạn muốn tắt tính năng này, hãy tắt WOL trong menu hệ thống BIOS hoặc bằng cách sau

`$# sudo ethtool -s <interface name> wol d`

- Bộ xử lý phản hồi MagicPacket có thể là 1 thành phần của card mạng hoặc Baseboard Management Controller (BMC).

# Công cụ quản lý Intel, Bộ điều khiển Hub và Minix
- BMC không phải là vi điều khiển duy nhất (MCU) có thể lắng nghe khi hệ thống ngủ. Các hệ thống x86_64 cũng bao gồm bộ phần mềm quản lý Intel (IME) để quản lý các hệ thống từ xa. Các thiết bị từ máy chủ đến máy tính xách tay đều có chức năng như KVM Remote Control. Theo công cụ Intel's own detection tool phát hiện IME có lỗ hổng chưa được vá. Đó là một thông tin xấu vì rất khó để ngắt IME. Trammell Hudson đã tạo một dự án là `me_cleaner` làm sạch một số thành phần IME.
- Phần mềm IME và phần mềm Quản lý Hệ thống (SMM) được khởi động được dựa trên hệ điều hành Minix và chạy trên Platform Controller Hub. SMM sau đó sẽ khởi chạy phần mềm Universal Extensible Firmware Interface (UEFI) trên bộ xử lý chính. Người dùng Linux bây giờ có thể mua máy tính xách tay từ Purism, System76 hoặc Dell với IME đã được disabled, ngoài ra chúng ta có thể hy vọng có máy tính xách tay có bộ vi xử lý ARM 64-bit.

# Bootloaders
- Công việc của bootloader là cung cấp cho hệ thống một bộ vi xử lý được hỗ trợ tài nguyên cần thiết để chạy một hệ điều hành như Linux. Khi bật nguồn, không chỉ không có bộ nhớ ảo mà còn không có DRAM cho đến khi bộ điều khiển của nó được bật lên. Bootloader sau khi được bật nguồn điện sẽ quét các bus và interfaces để xác định vị trí kernel và root filesystem. Các bootloader phổ biến như U-Boot và GRUB hỗ trợ các thiết bị quen thuộc như USB, PCI, và NFS. Bootloader cũng tương tác với các thiết bị bảo mật phần cứng như Trusted Platform Modules (TPM).

<img src="https://i.imgur.com/uWRBcWV.png">

- Là mã nguồn mở, bootloader U-Boot được sử dụng rộng rãi được hỗ trợ trên các hệ thống từ Raspberry Pi tới thiết bị Nintendo. Nó không có syslog. Để có tính năng gỡ lỗi, nhóm U-Boot cung cấp một sandbox, trong đó các bản vá lỗi có thể được kiểm tra trên máy chủ lưu trữ. Việc sử dụng sandbox của U-Boot tương đối đơn giản trên một hệ thống mà các công cụ phát triển phổ biến như Git và GNU Compiler Collection (GCC) đã được cài đặt:
```
$# git clone git://git.denx.de/u-boot; cd u-boot
$# make ARCH=sandbox defconfig
$# make; ./u-boot
=> printenv
=> help
```
- Bạn đang chạy U-Boot trên x86_64 và bạn có thể sử dụng các tính năng như phân vùng lại thiết bị lưu trữ. Các sandbox U-Boot có thể được dựa theo trình gỡ lỗi GDB. Việc sử dụng sandbox nhanh hơn 10 lần so với kiểm tra bằng cách nạp lại bootloader, và sandbox có thể được phục hồi với Ctrl + C.

# Starting up the kernel
- Các yếu tố cung cấp để khởi động kernel
- Sau khi hoàn thành nhiệm vụ, bootloader sẽ thực hiện một bước nhảy tới kernel mà nó đã nạp vào bộ nhớ chính và bắt đầu thực hiện nhiệm vụ mới thông các dòng lệnh mà người dùng đã chỉ định. file `/boot/vmlinuz` nó là bzImage, có nghĩa là một file nén lớn. Linux cung cấp công cụ `extract-vmlinux` có thể được sử dụng để giải nén file này: 
```
$# scripts/extract-vmlinux /boot/vmlinuz-$(uname -r) > vmlinux
$# file vmlinux 
vmlinux: ELF 64-bit LSB executable, x86-64, version 1 (SYSV), statically 
linked, stripped
```
- Kernel là một dạng nhị phân thực thi và liên kết (ELF) giống như các chương trình dành cho người sử dụng linux. Điều đó có nghĩa là chúng ta có thể sử dụng lệnh từ gói `binutils` như lệnh `readelf` để kiểm tra. Ví dụ:

```
$# readelf -S /bin/date
$# readelf -S vmlinux
```
- Danh sách các phần trong tập tin nhị phân phần lớn là tương tự nhau.
- Trước khi hàm main () có thể chạy, các chương trình cần một ngữ cảnh thực thi bao gồm heap và stack bộ nhớ như stdio, stdout, và stderr. Các chương trình của người dùng cần được cung cấp tài nguyên từ các thư viện chuẩn, đó là glibc trên hầu hết các hệ thống Linux. Ví dụ nghiên cứu các thứ dưới đây:
```
$# file /bin/date 
/bin/date: ELF 64-bit LSB shared object, x86-64, version 1 (SYSV), dynamically 
linked, interpreter /lib64/ld-linux-x86-64.so.2, for GNU/Linux 2.6.32, 
BuildID[sha1]=14e8563676febeb06d701dbee35d225c5a8e565a,
stripped
```

- Các chương trình ELF có một trình thông dịch , giống như các tập lệnh Bash và Python, nhưng thông dịch viên không cần phải được chỉ định với #! như trong các kịch bản. Trình thông dịch ELF cung cấp một số nhị phân với các nguồn lực cần thiết bằng cách gọi _start (), một hàm có sẵn từ gói nguồn glibc có thể kiểm tra thông qua GDB. Kernel rõ ràng là không có trình thông dịch, vậy nó đã hoạt động như thế nào?
- Kiểm tra khởi động của kernel với GDB sẽ có câu trả lời. Đầu tiên cần cài đặt gói gỡ lỗi cho kernel có chứa một phiên bản của `vmlinux`, ví dụ như `apt-get install linux-image-amd64-dbg` hoặc biên dịch và cài đặt kernel của riêng bạn từ nguồn. GDB sẽ chỉ ra rằng kernel x86_64 khởi động trong tập tin kernel arch/x86/kernel/head_64.S, nơi mà chúng ta tìm thấy hàm `start_cpu0 ()` và giải nén zImage trước khi gọi hàm start_kernel () x86_64. ARM 32-bit kernels tương tự như `arch/arm/kernel/head.S`. `start_kernel ()` không phải là kiến trúc cụ thể, vì vậy hàm này tồn tại trong `init/main.c` của kernel. `start_kernel()` được cho là hàm main() của Linux().

# From start_kernel() to PID 1
- Khi khởi động, kernel cần thông tin về phần cứng ngoại trừ processr mà nó đã được biên dịch. Các bước trong code được gia tăng bởi dữ liệu cấu hình được lưu trữ riêng. Có hai phương pháp chính để lưu trữ dữ liệu này: device-trees và ACPI tables. Kernel học được phần cứng nào nó phải chạy ở mỗi lần khởi động bằng cách đọc các tập tin này.
- Đối với thiết bị nhúng, device-tree là thể hiện của phần cứng được cài đặt. device-tree chỉ đơn giản là một file được biên dịch cùng lúc với nguồn kernel và thường nằm trong `/boot` cùng với `vmlinux`. Để xem những gì trong device-tree nhị phân trên một thiết bị ARM, chỉ cần sử dụng lệnh `strings` từ gói `binutils` trên một file có tên tương ứng /boot/*.dtb. Rõ ràng device-tree có thể được sửa đổi đơn giản bằng cách chỉnh sửa các tệp JSON giống như tạo file và chạy lại trình biên dịch `dtc` được cung cấp cùng với mã nguồn kernel. Mặc dù device-tree là một tệp tin tĩnh mà đường dẫn tệp thường được kernel truyền đến bởi trình nạp khởi động trên dòng lệnh, một cơ chế che phủ device-tree đã được thêm vào trong những năm gần đây, nơi kernel có thể tự động nạp các mảnh bổ sung để đáp ứng hotplug sự kiện sau khi khởi động.
- x86-family và nhiều thiết bị ARM64 cấp doanh nghiệp sử dụng cơ chế Advanced Configuration and Power Interface (ACPI) thay thế. Trái ngược với device-tree, thông tin ACPI được lưu trữ trong hệ thống tập tin ảo của hệ thống /sys/firmware/acpi/tables được tạo bởi kernel khi khởi động bằng cách truy cập vào ROM trên máy. Cách dễ dàng để đọc bảng ACPI là với lệnh ernel`acpidump` từ gói công cụ `acpica`. Đây là một ví dụ:

<img src="https://i.imgur.com/uWRBcWV.png">

- Vâng, hệ thống Linux của bạn đã sẵn sàng cho Windows 2001, bạn nên cẩn thận khi cài đặt nó. ACPI có cả phương thức và dữ liệu, không giống như device-tree, vốn là ngôn ngữ mô tả phần cứng. Các phương thức của ACPI tiếp tục là hoạt động sau khi khởi động. Ví dụ, bắt đầu lệnh `acpi_listen` (từ gói `apcid`) và mở và đóng nắp laptop sẽ thấy rằng chức năng ACPI đang chạy tất cả thời gian. Nếu muốn thay đổi chúng liên quan đến tương tác với trình đơn BIOS lúc khởi động hoặc nạp lại ROM. Nếu bạn đang gặp rắc rối đó, có lẽ bạn nên cài đặt `coreboot`, phần mềm nguồn mở thay thế.


# From start_kernel() to userspace
- Code trong `init/main.c` khá dễ hiểu, vẫn mang bản quyền gốc của Linus Torvalds từ năm 1991-1992. Các dòng tìm thấy trong `dmesg | head` vào một hệ thống mới khởi động bắt nguồn chủ yếu từ tập tin nguồn này. CPU đầu tiên được xác nhận với hệ thống, các cấu trúc dữ liệu global được khởi tạo, và trình lập lịch, bộ xử lý gián đoạn (IRQs), bộ đếm thời gian và giao diện điều khiển được đặt theo trình tự.  Cho đến khi chức năng timekeeping_init() chạy, tất cả các dấu thời gian bắt đầu từ 0. Phần khởi tạo kernel này đồng bộ, có nghĩa là sự thực hiện xảy ra trong chính xác một luồng và không có hàm nào được thực hiện cho đến khi kết thúc và trả về kết quả cuối cùng. Kết quả là, đầu ra `dmesg` sẽ được tái tạo hoàn toàn, ngay cả giữa hai hệ thống, miễn là họ có cùng một thiết bị cây hoặc bảng ACPI. Linux đang hoạt động giống như một trong những hệ điều hành RTOS (hệ điều hành thời gian thực) chạy trên các MCU, ví dụ như QNX hoặc VxWorks. Tình huống vẫn tồn tại trong hàm `rest_init()`, được gọi bởi `start_kernel()` tại thời điểm chấm dứt.

<img src="https://i.imgur.com/em0OT2w.png">

- Hàm `Rest_init()` tạo ra một luồng mới chạy để chạy `kernel_init()`, nó sẽ gọi `do_initcalls()`. Người dùng có thể theo dõi `initcalls` bằng cách thêm `initcall_debug` vào dòng lệnh kernel, dẫn đến các mục `dmesg` mỗi khi một chức năng `initcall` chạy. `initcalls` đi qua bảy cấp độ tuần tự: early, core, postcore, arch, subsys, fs, device, and late. Phần người dùng có thể nhìn thấy nhất là các thiết bị ngoại vi: bus, mạng, lưu trữ, màn hình hiển thị ... cùng với việc tải các mô-đun hạt nhân của họ. rest_init () cũng tạo ra một luồng thứ hai trên bộ vi xử lý khởi động bắt đầu bằng cách chạy `cpu_idle()` trong khi nó chờ cho trình lên lịch gán nó làm việc.
- Lưu ý rằng code trong `init/main.c` gần như đã hoàn thành khi chạy hàm `smp_init ()`: Bộ xử lý khởi động đã hoàn thành hầu hết việc khởi tạo một lần mà các cores khác không cần phải lặp lại. Tuy nhiên, các luồng cho mỗi CPU phải được sinh ra cho mỗi core để quản lý các ngắt (IRQs), hàng đợi công việc, timer, và các sự kiện quyền lực trên mỗi. Ví dụ, xem các chuỗi cho mỗi CPU đang hoạt động thông qua lệnh `ps -o psr`.
```
$\# ps -o pid,psr,comm $(pgrep ksoftirqd)  
 PID PSR COMMAND 
   7   0 ksoftirqd/0 
  16   1 ksoftirqd/1 
  22   2 ksoftirqd/2 
  28   3 ksoftirqd/3 

$\# ps -o pid,psr,comm $(pgrep kworker)
PID  PSR COMMAND 
   4   0 kworker/0:0H 
  18   1 kworker/1:0H 
  24   2 kworker/2:0H 
  30   3 kworker/3:0H
[ . .  . ]
```

- PSR là viết tắt của "processor".
- `Kernel_init()` tìm kiếm một initrd có thể thực hiện quá trình init thay cho nó. Nếu nó không tìm thấy, kernel trực tiếp thực hiện init.

# Early userspace: who ordered the initrd?
- Bên cạnh device-tree, một đường dẫn file khác được cung cấp cho kernel khi khởi động là file initrd. Initrd thường lưu trong `/boot` cùng với file `bmlIcon` của `bzImage` trên x86 hoặc cùng với `uImage` và device-tree tương tự cho ARM. Xem nội dung của `initrd` bằng công cụ `lsinitramfs` là một phần của gói `initramfs-tools-core`. `initrd` chứa thư mục `/bin`, `/sbin`, và `/etc` cùng với các mô-đun kernel, cùng với một số tệp trong `/script`. initrd phần lớn chỉ đơn giản là một hệ thống tập tin gốc Linux rút gọn. Gần như tất cả các file thực thi trong `/bin` và `/sbin` bên trong ramdisk là các liên kết đến binary BusyBox.
- Tại sao lại phải tạo ra `initrd` nếu tất cả những gì nó làm là nạp một số mô-đun và sau đó bắt đầu `init` trên hệ thống tập tin gốc thông thường? Xem xét một hệ thống tập tin root đã mã hóa. Giải mã có thể dựa vào việc tải một mô-đun kernel được lưu trữ trong /lib/modules trên hệ thống tập tin root ...  Mô đun crypto có thể được biên dịch theo thống kê vào kernel thay vì được tải từ một tệp nhưng có nhiều lý do khiến bạn không muốn làm như vậy. Ví dụ, việc biên dịch hạt nhân bằng các mô đun có thể làm cho nó quá lớn để phù hợp với dung lượng có sẵn, hoặc việc biên dịch tĩnh có thể vi phạm các điều khoản của một giấy phép phần mềm. Các trình điều khiển thiết bị đầu vào (HID) của người lưu trữ, mạng và thiết bị đầu vào người dùng (HID) cũng có thể có trong initrd-về cơ bản bất kỳ mã nào không phải là một phần của kernel thích hợp để gắn kết hệ thống tập tin gốc. Initrd cũng là nơi người dùng có thể lưu trữ mã bảng ACPI tuỳ chỉnh của riêng họ.

<img src="https://i.imgur.com/KyAkJVA.png">

`initrd` là rất hữu ích cho việc thử nghiệm các hệ thống tập tin và các thiết bị lưu trữ dữ liệu. Giữ các công cụ kiểm tra này trong `initrd` và chạy thử nghiệm của bạn từ bộ nhớ hơn là từ đối tượng được thử.
- Cuối cùng, khi init chạy, hệ thống đang bắt đầu! Kể từ khi các bộ vi xử lý thứ 2 đang chạy, máy đã không thể dự đoán trước được, hiệu năng. Thật vậy, `ps -o pid`, `psr`, `comm -p 1` có thể chứng minh rằng tiến trình init của không gian người dùng không còn chạy trên bộ xử lý khởi động nữa.

# Summary
- Quá trình khởi động Linux nghe có vẻ không được động vào, nhìn cách khác, quá trình khởi động khá đơn giản, vì sự phức tạp do các tính năng như preemption, RCU không có trong khi khởi động. Chỉ tập trung vào hạt nhân và PID 1 là nhìn thấy có số lượng lớn công việc mà bootloaders và bộ vi xử lý phụ có thể làm trong việc chuẩn bị nền tảng cho kernel để chạy. Trong khi kernel chắc chắn là duy nhất trong số các chương trình Linux, một số hiểu biết về cấu trúc của nó có thể được hiểu bằng cách áp dụng cho nó một số công cụ tương tự dùng để kiểm tra các chương trình ELF khác. Nghiên cứu quá trình khởi động trong khi nó đang làm việc để bảo vệ hệ thống khi gặp sự cố.
