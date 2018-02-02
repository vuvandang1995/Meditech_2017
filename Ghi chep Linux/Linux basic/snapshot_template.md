### <a name="template"> 1. Hướng dẫn tạo Template từ VM </a>

#### <a name="gioi-thieu">1.1 Giới thiệu về Template trong KVM </a>

- Template là một dạng file image pre-configured của hệ điều hành được dùng để tạo nhanh các máy ảo. Sử dụng template sẽ khiến bạn tránh khỏi những bước cài đặt lặp đi lặp lại và tiết kiệm thời gian rất nhiều so với việc cài bằng tay từng bước một.
- Giả sử bạn có 4 máy Apache web server. Thông thường, bạn sẽ phải cài 4 máy ảo rồi lần lượt cài hệ điều hành cho từng máy, sau đó lại tiếp tục tiến hành cài đặt dịch vụ hoặc phần mềm. Điều này tốn rất nhiều thời gian và template sẽ giúp bạn giải quyến vấn đề này.
- Hình dưới đây mô tả các bước mà bạn phải thực hiện theo ví dụ trên nếu bạn cài bằng tay. Rõ ràng từ bước 2-5 chỉ là những tasks lặp đi lặp lại và nó sẽ tiêu tốn rất nhiều thời gian không cần thiết.

<img src="http://i.imgur.com/yPEbPLj.png">

- Với việc sử dụng template, số bước mà người dùng phải thực hiện sẽ được rút ngắn đi rất nhiều, chỉ cần thực hiện 1 lần các bước từ 1-5 rồi tạo template là bạn đã có thể triển khai 4 web servers còn lại một cách rất dễ dàng. Điều này sẽ giúp người dùng tiết kiệm rất nhiều thời gian:

<img src="http://i.imgur.com/itJRUFQ.png">

#### <a name="create-template"> 1.2 Hướng dẫn tạo và quản lý template </a>

- Hai khái niệm mà người dùng cần phân biệt đó là `clone` và `template`. Nếu `clone` đơn thuần chỉ là một bản sao của máy ảo thì `template` được coi là master copy của VM, nó có thể được dùng để tạo ra rất nhiều clone khác nữa.
- Có hai phương thức để triển khai máy ảo từ template đó là `Thin` và `Clone`
  <ul>
  <li>Thin: Máy ảo được tạo ra theo phương thức này sẽ sử dụng template như một base image, lúc này nó sẽ được chuyển sang trạng thái read only. Cùng với đó, sẽ có một ổ mới hỗ trợ "copy on write" được thêm vào để lưu dữ liệu mới. Phương thức này tốn ít dung lượng hơn tuy nhiên các VM được tạo ra sẽ phụ thuộc vào base image, chúng sẽ không chạy được nếu không có base image</li>
  <li>Clone: Máy ảo được tạo ra làn một bản sao hoàn chỉnh và hoàn toàn không phụ thộc vào template cũng như máy ảo ban đầu. Mặc dù vậy, nó sẽ chiếm dung lượng giống như máy ảo ban đầu.</li>
  </ul>
**Hướng dẫn tạo templates**

- Template thực chất là máy ảo được chuyển đổi sang. Quá trình này gồm 3 bước:
  <ul>
  <li>Bước 1: Cài đặt máy ảo với đầy đủ các phần mềm cần thiết để biến nó thành template</li>
  <li>Bước 2: Loại bỏ tất cả những cài đặt cụ thể ví dụ như password SSH, địa chỉ MAC... để đảm bảo rằng nó sẽ không được áp dụng giống nhau tới tất cả các máy ảo được tạo ra sau này.</li>
  <li>Bước 3: Đánh dấu máy ảo là template bằng việc đổi tên.</li>
  </ul>