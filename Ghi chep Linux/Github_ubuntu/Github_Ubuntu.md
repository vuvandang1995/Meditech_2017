# Các thao tác với github trên Linux
## Cài đặt
Trên Ubuntu:
`apt-get install git`
Trên Centos:
`yum install git`
## Các thiết lập khởi đầu
- Sau khi cài Git xong, việc đầu tiên bạn nên làm là khai báo tên và địa chỉ email vào trong file cấu hình của Git trên máy. Để làm điều này bạn sẽ cần sử dụng hai lệnh sau đây để thiết lập tên và email.
```
$ git config --global user.name "vuvandang1995"
$ git config --global user.email "dangdiendao@gmail.com"om
```
- Sau khi thiết lập xong, bạn có thể kiểm tra thông tin chứng thực trên user của bạn bằng cách xem tập tin `~/.gitconfig` (nhắc lại rằng dấu ~ nghĩa là thư mục gốc của user).
`cat ~/.gitconfig`
<img src="https://i.imgur.com/4APlpqY.png">

- Lựa chọn trình soạn thảo mặc định, có thể là vi, vim, nano,...
`git config --global core.editor vi`

- Lệnh git `config --list` để ghi danh sách các thiết lập hiện tại mà bạn đã làm.
<img src="https://i.imgur.com/B2k70cf.png">

## Xác thực bảo mật tài khoản github bằng key SSH
Hoàn thành các bước này, sẽ giúp bạn trong 2 việc:
1. Bảo mật các kết nối của bạn với server.
2. Không phải nhập mật khẩu mỗi lần push code.

- Tạo key ssh với lệnh `ssh-keygen -t rsa`
<img src="https://i.imgur.com/9vLWPFt.png">

Nếu bạn nhập passphrase thì hãy nhớ pass này!
Kiểm tra kết quả:
<img src="https://i.imgur.com/RQn4WPP.png">

Add `private key` vào `ssh-agent`:
`ssh-agent ~/.ssh/id_rsa`

Bạn mở file `id_rsa` và copy đoạn mã đó.
Tiếp theo, truy cập đường dẫn sau: https://github.com/settings/ssh (đảm bảo bạn đã đăng nhập vào github), chọn Add SSH key, đặt tên cho key này tại Title và paste nội dung vừa copy vào ô Key.
<img src="https://i.imgur.com/k0fe1VX.png">

Sau khi add key thành công, kết quả sẽ thế này:
<img src="https://i.imgur.com/vr1jKAa.png">

## Các thao tác với Repo
- Tạo một repo mới trên trang github.com
<img src="https://i.imgur.com/Cyg7Zpj.png">

<img src="https://i.imgur.com/2Kuzuug.png">

- Clone Repo
Clone một Repo bằng các cách sau:
**C1: SSH**: `git clone git@github.com:vuvandang1995/linux.git`
hoặc: `git clone git@github.com:vuvandang1995/linux.git /opt/demo` để clone vào thư mục /opt/demo

**Lưu ý:** đối với phương pháp này các bạn cần nhập passphrase của ~/.ssh/id_rsa (có thể không cần nếu bạn không đặt passphrase)

**C2: HTTPS** `git clone https://github.com/vuvandang1995/linux.git`
hoặc: `git clone https://github.com/vuvandang1995/linux.git /opt/demo` để clone vào thư mục /opt/demo

Để lấy các link SSH, HTTPS này ta làm như sau: Click vào các hyperlink HTTPS hoặc SSH rồi click Copy to clipboard.
<img src="https://i.imgur.com/KLKiFhk.png">


Giả sử tôi sử dụng lệnh 
`git clone git@github.com:vuvandang1995/linux.git`
Khi đó, máy tính của tôi đã được clone về một thư mục có tên là `linux`
Làm việc trên thư mục này:
```
cd linux/
```
Bạn có thể thêm dữ liệu của bạn vào thư mục `linux`. Giả sử tôi tạo 1 file `readme1.md` có nội dung: Xin chao moi nguoi
<img src="https://i.imgur.com/hfKPwsO.png">

## Các thao tác  Add, push, commit

Trước khi push 1 dữ liệu từ máy local lên github, bạn cần di chuyển vào thư mục đã clone
<img src="https://i.imgur.com/fnIlSrM.png">

Sử dụng lệnh `git init` để khởi động
<img src="https://i.imgur.com/48zVFmM.png">

Sử dụng lệnh `git add ...` để add dữ liệu cần push lên server
Giả sử tôi add file `readme1.md` vừa tạo:
<img src="https://i.imgur.com/n8a5xFL.png">

hoặc: `git add *` để add hết tất cả dữ liệu trong thư mục clone.

Sau khi add dữ liệu, sử dụng lệnh `commit`. Ví dụ: `git commit readme1.md`
hoặc: `git commit *` để commit tất cả dữ liệu đã add. Ta nên thêm tham số -m để ghi lại một comment cho hành động đó. Ví dụ: `git commit readme1.md -m "update file readme1.md"`

<img src="https://i.imgur.com/IKKeCV6.png">


Lúc này các thay đổi của bạn đã được lưu lại trên máy cục bộ. Để `push` lên server Github ta thực hiện lệnh:
`git push origin master`

<img src="https://i.imgur.com/kvJEUK2.png">

Lúc này trở lại trang github.com và xem các commit của ta đã được đẩy lên.

<img src="https://i.imgur.com/fas22wY.png">


- Pull dữ liệu từ server về local
Giả sử trên server github của bạn có những thay đổi mà máy local chưa cập nhật những thay đổi đó. Bạn thực hiện lệnh sau:
<img src="https://i.imgur.com/lEZBFLK.png">

## Các bước tham gia nhóm làm việc trên github
B1: Fork project cần tham gia vào github cá nhân
Truy cập vào trang của project, chọn `Fork`
<img src="https://i.imgur.com/F29TxnU.png">

B2: Clone project cần tham gia về. Ví dụ tôi muốn tham gia project nhóm thực tập meditech
Clone project.
`git clone git@github.com:meditechopen/meditech-thuctap.git`

Truy cập vào thư mục vừa clone về, thêm các dữ liệu cá nhân vào thư mục đó.
Giả sử tôi thêm thư mục `DangVV` và project trên.
B3: Push dữ liệu lên 
Làm các thao tác như push dữ liệu lên github cá nhân như các lệnh
`git init`
`git add ...`
`git commit ...`
`git push origin master`

như thao tác `git push origin maste` bạn cần nhập username và password tài khoản github của bạn.
<img src="https://i.imgur.com/WyVcdqc.png">
B4: Tạo pull request trên project
Truy cập vào trang của project đã fork về, chọn `New pull request`
<img src="https://i.imgur.com/rRfKb8i.png">

Tiếp theo, chọn `Create pull request`và chờ admin project duyệt.

## Xóa một thư mục trên github
Để xóa một thư mục trên github web, bạn cần  thực hiện xóa thư mục đó tại thư mục gốc mà đã clone về máy local.
Các bước để xóa một thư mục trên github web như sau:
- B1: Clone project đó về máy clone
- B2: Thực hiện các lệnh
  - `git init`
  - `git rm -r --cached folder`
  - `git commit -m "remove folder"`
  - `git push origin master`
- B3: Kiểm tra trên github web đã được xóa chưa.

