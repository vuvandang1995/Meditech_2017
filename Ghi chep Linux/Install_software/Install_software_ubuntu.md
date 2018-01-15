# Installing Software
## Giới thiệu
Cài đặt phầm mềm trên Ubuntu là dễ dàng và bài viết này sẽ giúp bạn hiểu công việc đó hoạt động như thế nào.
Theo mặc định, nhiều chương trình hữu ích đã được cài đặt khi bạn dắt đầu cài hệ điều hành Ubuntu. Tuy nhiên, những ứng dụng mặc định đó chưa đáp ứng được nhu cầu của bạn, hay nói cách khác là bạn cần cài đặt một phần mềm mới.
Nếu bạn muốn biết các thông số cơ bản hay những gì đã xảy ra khi bạn cài đặt phần mềm, hãy mở và đọc các package và package management.
## Package và Package management là gì?
Phần này trình bày các khái niệm về Package và Package management, sự khác nhau những các loại Package các cách quản lý các package phần mềm trên Ubuntu.
- Package là gì?
Phần mềm là một thuật ngữ rất rộng và được hiểu là một chương trình mà bạn có thể chạy trên máy tính cá nhân của mình. Tuy nhiên, khi bạn cài đặt một phần mềm, có rất nhiều file được yêu cầu để phục vụ cho việc chạy chương trình.
Ubunu sử dụng các Package để lưu trữ tất cả mọi thứ mà một chương trình cần để chạy. Một Package là một tập hợp các file được đóng gói vào một file duy nhất, từ đó có thể xử lý công việc dễ dàng. Ngoài các file cần thiết để cho chương trình chạy, còn có các file đặc biệt, đó là tập các lệnh cài đặt.
## Source or Binary
Thông thường, khi đóng gói cho một chương trình, họ thường đặt tất cả các source code của chương trình đó vào package. Source code được viết bởi các coder. Máy tính có thể hiểu được những đoạn code này bằng cách dịch hoặc biên dịch code thành hệ nhị phân mà máy tính có thể xử lý được.
Một câu hỏi được đặt ra là: tại sao không đóng các package thành hệ nhị phân ngay từ đầu? Lí do là các máy tính khác nhau sử dụng các loại nhị phân khác nhau.
**Source packages** đơn giản là các package chứa source code và thường có thể sử dụng được trên hầu hết các máy nếu có trình biên dịch mã đó phù hợp
**Binary packages** lợà những package đã được biên dịch bởi một máy tính nào đó. Ubuntu hỗ trợ x86 (i386 hoặc i686), AMD64 và PPC. Các gói nhị phân chính xác sẽ được sử dụng tự động.
## Package Dependencies
Các chương trình khác nhau thường sử dụng một số file giống nhau. Chính vì thế, thay vì thêm các file này vào từng package riêng cho mỗi chương trình, một package riêng được đóng gói để cung cấp các file cho tất cả các chương trình cần dùng đến chúng. Vì vậy, để cài đặt một chương trình cần những package này, gọi là `package dependenes`. Tùy vào mối quan hệ phụ thuộc, các gói phụ thuộc có thể được đóng gói đơn giản hơn.
Khi bạn cài đặt một chương trình, các package depend cũng phải được cài cùng lúc. Thông thường, hầu hết các package depend bắt buộc được cài đặt, nhưng có thể cần thêm một vài tính năng bổ sung, vì thế khi bạn cài đặt một package đừng ngạc nhiên khi một số gói 
