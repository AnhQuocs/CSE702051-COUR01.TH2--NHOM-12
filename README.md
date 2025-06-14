<p align="center"><strong><font size="12">WEBSITE QUẢN LÝ DỰ ÁN CÁ NHÂN - MY PROJECT HUB</font></strong></p>

Website quản lý dự án cho từng user cá nhân, backend cung cấp các API để:
- Xác thực người dùng (đăng ký, đăng nhập, đăng xuất)
- Lấy, tạo, cập nhật, xóa dự án
- Lọc dự án theo trạng thái, mức độ ưu tiên
- Thống kê số lượng, tỉ lệ phần trăm dự án hoàn thành, chưa hoàn thành, quá hạn
- Cập nhật thông tin người dùng

---

## 1. Mô hình dữ liệu

### User
| Trường    | Kiểu dữ liệu | Mô tả                       |
|-----------|--------------|-----------------------------|
| id        | String       | ID duy nhất của user        |
| username  | String       | Tên đăng nhập               |
| email     | String       | Email tài khoản             |
| password  | String       | Mật khẩu                    |

### Project
| Trường         | Kiểu dữ liệu | Mô tả                                                        |
|----------------|--------------|--------------------------------------------------------------|
| id             | String       | ID duy nhất của dự án                                        |
| userId         | String       | ID của user sở hữu dự án                                     |
| title          | String       | Tiêu đề dự án                                                |
| description    | String       | Mô tả chi tiết dự án                                         |
| priority       | String       | Độ ưu tiên: Thấp, Trung bình, Cao                            |
| status         | String       | Trạng thái: Lên kế hoạch, Đang thực hiện, Đã hoàn thành, Hoàn thành muộn |
| deadline       | Date         | Ngày kết thúc, hạn hoàn thành                                |
| completed_late | Boolean      | true nếu hoàn thành muộn, false nếu hoàn thành đúng hạn      |

---

## 2. Project API
**GET /api/projects**
- Mô tả: Lấy danh sách tất cả dự án của user hiện tại. Có thể lọc theo priority, status.
- Query params: ?priority=...&status=...
- Response thành công (200 OK):
```json
[
  {
    "id": "projec001",
    "userId": "user123",
    "title": "Project 1",
    "description": "Des project 1",
    "priority": "Trung bình",
    "status": "Đang thực hiện",
    "deadline": "2025-06-29",
    "completed_late": false
  },
  {
    "id": "projec002",
    "userId": "user123",
    "title": "Project 2",
    "description": "Des project 2",
    "priority": "Cao",
    "status": "Đã hoàn thành",
    "deadline": "2025-06-29",
    "completed_late": false
  }
]
```

---

**GET /api/project-stats**
- Mô tả: Thống kê số lượng, tỉ lệ phần trăm dự án hoàn thành, chưa hoàn thành, quá hạn.
- Response thành công (200 OK):
```json
{
  "total": 10,
  "completed": 4,
  "incomplete": 6,
  "overdue": 2,
  "completed_percent": 40.0,
  "incomplete_percent": 60.0,
  "overdue_percent": 20.0
}
```

---

**POST /api/projects**
- Mô tả: Tạo dự án mới. Dự án mặc định là chưa hoàn thành, nếu quá hạn mà chưa hoàn thành sẽ được tính là quá hạn.
- Request Body (JSON):
```json
{
  "title": "Project mới",
  "description": "Mô tả project mới",
  "priority": "Cao",
  "status": "Lên kế hoạch",
  "deadline": "2025-06-30"
}
```
- Response thành công (201 Created):
```json
{
  "id": "project003",
  "userId": "user123",
  "title": "Project mới",
  "description": "Mô tả project mới",
  "priority": "Cao",
  "status": "Lên kế hoạch",
  "deadline": "2025-06-30",
  "completed_late": false
}
```

---

**PUT /api/projects/{projectId}**
- Mô tả: Cập nhật dự án. Nếu chuyển sang hoàn thành sau hạn sẽ tự động chuyển trạng thái sang "Hoàn thành muộn".
- Request Body (JSON):
```json
{
  "title": "Cập nhật project",
  "description": "Mô tả cập nhật project",
  "priority": "Thấp",
  "status": "Đã hoàn thành",
  "deadline": "2025-07-01"
}
```
- Response thành công (200 OK):
```json
{
  "id": "project003",
  "userId": "user123",
  "title": "Cập nhật project",
  "description": "Mô tả cập nhật project",
  "priority": "Thấp",
  "status": "Hoàn thành muộn",
  "deadline": "2025-07-01",
  "completed_late": true
}
```

---

**DELETE /api/projects/{projectId}**
- Response thành công (204 No Content): Không trả về nội dung

---

## 3. Xác thực & Bảo mật
- Tất cả các API đều yêu cầu xác thực qua Sanctum (private API).
- Người dùng chỉ thao tác được với dự án của chính mình.

---

## 4. Hướng dẫn chạy dự án
1. Clone source code về máy
2. Cài đặt Composer và npm nếu chưa có
3. Chạy `composer install` và `npm install`
4. Copy file `.env.example` thành `.env` và cấu hình database
5. Chạy `php artisan key:generate`
6. Chạy `php artisan migrate:refresh` để tạo bảng
7. Chạy `npm run dev` để build frontend
8. Khởi động server: `php artisan serve`
9. Truy cập http://localhost:8000

---

## 5. Liên hệ & đóng góp
- Nếu có thắc mắc hoặc muốn đóng góp, vui lòng tạo issue hoặc pull request trên repository này.
