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
<img src="">

- Lựa chọn trình soạn thảo mặc định, có thể là vi, vim, nano,...
`git config --global core.editor vi`

- Lệnh git `config --list` để ghi danh sách các thiết lập hiện tại mà bạn đã làm.
<img src="">

## Xác thực bảo mật tài khoản github bằng key SSH
- Tạo key ssh với lệnh `ssh-keygen -t rsa`
<img src="">

Nếu bạn nhập passphrase thì hãy nhớ pass này!
Kiểm tra kết quả:
<img src="">

