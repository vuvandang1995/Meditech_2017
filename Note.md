## Năm 2018
- Tốt nghiệp đại học.
- Theo vào một công ty để học tập và nghiên cứu Openstack, python
- Các mục tiêu năm 2018
	1. Tìm hiểu và cố gắng làm chủ Openstack
	2. Code Python ở level khá
	3. Làm chủ và phát triển giải pháp KVM-VDI




# Mticket App
## Công nghệ sử dụng
1. Back-end: Python-Django
2. Font-end: Html, css, jquery
3. Database: Mysql
## Thành phần đặc biệt
1. Xử lý real-time,chat
- Sử dụng công nghệ websocket django channel [link](http://channels.readthedocs.io/en/latest/introduction.html)
- Sử dụng redis để lưu message qua lại của websocket (client - server)
2. Phơi API cho các service khác sử dụng (tạo ticket,..)
## Một số lưu ý.
1. Tìm hiểu thêm vể redis server: Redis là một hệ quản trị CSDL noSQL, lưu database theo kiểu key-value, truy vấn rất nhanh vì nó lưu tạm vào cache trước khi lưu xuống disk
2. Để dữ liệu được update real-time, cần sử dụng websocket và load lại các table thông tin. Để load lại table mà không ảnh hưởng gì tới các hiệu ứng css, javascript của table đó, cần sử dụng công nghê Datatables.
**Chi tiết**:
- Phía back-end sẽ trả về chuỗi json là thành phần html của table.

```
def manage_user_data(request):
    if request.session.has_key('agent')and(Agents.objects.get(username=request.session['agent'])).status == 1:
        users = Users.objects.all()
        data = []
        for us in users:
            if us.status == 0:
                st = r'''<p id="stt''' + str(us.id) +'''"><span class="label label-danger">inactive</span></p>'''
                option = r'''<p id="button''' + str(us.id) +'''"><button id="''' + str(us.id) + '''" class="unblock btn btn-success" type="button" data-toggle="tooltip" title="unblock" ><span class="glyphicon glyphicon glyphicon-ok" ></span> Unblock</button></p>'''
            else:
                st = r'''<p id="stt''' + str(us.id) +'''"><span class="label label-success">active</span></p>'''
                option = r'''<p id="button''' + str(us.id) +'''"><button id="''' + str(us.id) + '''" class="block btn btn-danger" type="button" data-toggle="tooltip" title="block" ><span class="glyphicon glyphicon-lock" ></span> Block</button></p>'''
            data.append([us.id, us.fullname, us.email, us.username, st, str(us.created)[:-13], option])
        ticket = {"data": data}
        tickets = json.loads(json.dumps(ticket))
        return JsonResponse(tickets, safe=False)
```

- Phía font-end sẽ dùng ajax để get chuỗi json đó và show ra.

```
$(document).ready(function(){
    $('#list_user').DataTable({
        "columnDefs": [
            { "width": "5%", "targets": 0 },
            { "width": "20%", "targets": 1 },
            { "width": "25%", "targets": 2 },
            { "width": "10%", "targets": 3 },
            { "width": "5%", "targets": 4 },
            { "width": "20%", "targets": 5 },
            { "width": "15%", "targets": 6 },
        ],
        "ajax": {
            "type": "GET",
            "url": location.href +"data",
            "contentType": "application/json; charset=utf-8",
            "data": function(result){
                return JSON.stringify(result);
            }
        },
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        "order": [[ 0, "desc" ]],
        "displayLength": 25,
    });
```

- Khi có message từ websocket thông báo có sự thay đổi,cần load lại tables thì dùng lệnh `$("#list_user").DataTable().ajax.reload();`

3. Để project có thể kết nối tới database,cần cấu hình trong file setting.py và tạo user,cấp quyền cho user trên database server, check port 3306

4. Để sử dụng được các event jquery sau khi load lại trang,cần gọi từ thẻ cha của chúng trước
- Ví dụ: 
```
$("body").on('click', '.handle_done', function(){
        var id = $(this).attr('id');
        var token = $("input[name=csrfmiddlewaretoken]").val();
        if(confirm("Are you sure ?")){
...
```
