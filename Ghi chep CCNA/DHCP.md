## Giới thiệu
- DHCP là Dynamic Host Configuration Protocol
- DHCP được sử dụng để điều khiển cấu hình mạng của các host thông qua một server từ xa.
- DHCP là một tính năng được cài mặc định trong hầu hết các hệ điều hành.
- DHCP là một sự lựa chọn tuyệt vời mà không tốn thời gian để cấu hình mạng cho một host hoặc một mạng các thiết bị
- DHCP làm việc trên mô hình server-client. Trong giao thức này, nó thiết lập các message để trao đổi giữa client và server. Dưới đây là thông tin các header của DHCP

<img src="http://l4wisdom.com/linux-with-networking/images/dhcp/dhcp.jpg">

## DHCP làm việc như thế nào?
Trước khi học DHCP, tôi muốn bạn hiểu đầu tiên về sự khác nhau giữa các message được sử dụng trong tiến trình.
### 1. DHCP discover
Nó là message khởi động của cho sự bắt đầu tương tác DHCP giữa server và client. Message này được gửi bởi client (là host hoặc thiết bị được kết nối trong mạng). Nó là một tin broadcast sử dụng 255.255.255.255 như là địa chỉ IP đích trong khi đó, IP nguồn là 0.0.0.0
### 2. DHCP offer
Nó là message được gửi để phản hồi cho message DHCP discover bởi DHCP server cho DHCP client. Message này chứa các cài đặt cấu hình mạng cho Client đã gửi DHCP discover cho nó.
### 3. DCHP request
Đây là DHCP message được gửi để phản hồi lại message DHCP offer rằng client đồng ý với các thông tin cài đặt cấu hình mạng đã được gửi trong DHCP offer bởi server.
### 4. DHCP ack
Đây là message được gửi bởi DHCP server để phản hồi lại DHCP request nhận được từ client. Message này đánh dấu sự kết thúc của quá trình với DHCP cover. DHCP ack bên trong không có gì cả nhưng nó là cách để DHCP cho phép rằng DHCP client được sử dụng các cài đặt cấu hình mạng mà nó đã gửi từ DHCP offer.
### 5. DHCP nak
Đây là thông báo trái ngược với DHCP ack. Thông báo này được gửi bởi DHCP server khi nó không thể đáp ứng DHCP request của client.
### 6. DCHP decline
Thông báo này được gửi từ client trong trường hợp nó phát hiện ra địa chỉ IP mà nó nhận được từ DHCP server đã được sử dụng.

## Cơ chế hoạt động
- Bước 1: Khi một Client khởi động hoặc kết nối vào mạng, một thông điệp DHCP discover được gửi từ client đến server. Taị thời điểm đó, client không có các thông tin cấu hình mạng, vì thế thông điệp mà nó gửi đi với địa chỉ nguồn là 0.0.0.0 và địa chỉ đích là 255.255.255.255. Nếu DHCP server nằm trên mạng local thì nó sẽ nhận ngay được thông điệp đó, hoặc trường hợp DHCP server thuộc subnet khác thì thông điệp sẽ được chuyển tiếp từ subnet của client tới DHCP server. DHCP sử dụng giao thức UDP và port 67.
- Bước 2: Khi DHCP server nhận được DHCP discover thì nó sẽ trả lời với thông báo DHCP offer. Như đã giải thích bên trên, thông báo này chứa tất cả các cài đặt cấu hình mạng cho client và được gửi lại cho client với địa chỉ MAC mà nó đã biết, sử dụng UDP và port 68.
- Bước 3: Khi client nhận được DHCP offer, nó sẽ trả lời với DHCP request cho server với thông báo là nó đồng ý với các cài đặt mà nó nhận được từ DHCP offer.
- Bước 4: Đây là message được gửi bởi DHCP server để phản hồi lại DHCP request nhận được từ client. Message này đánh dấu sự kết thúc của quá trình với DHCP cover. DHCP ack bên trong không có gì cả nhưng nó là cách để DHCP cho phép rằng DHCP client được sử dụng các cài đặt cấu hình mạng mà nó đã gửi từ DHCP offer.
