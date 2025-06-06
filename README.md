<p align="center"><strong><font size="12">WEBSITE QUẢN LÝ DỰ ÁN CÁ NHÂN - MY PROJECT HUB</font></strong></p>

Website quản lý dự án cho từng user cá nhân, backend cung cấp các API để:
- Xác thực người dùng (đăng nhập, đăng xuất)
- Lấy dữ liệu dự án
- Cập nhật dự án
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
| Trường      | Kiểu dữ liệu | Mô tả                    |
|-------------|--------------|--------------------------|
| id          | String       | ID duy nhất của dự án    |
| userId      | String       | ID của user sở hữu dự án |
| title       | String       | Tiêu đề dự án            |
| description | String       | Mô tả chi tiết dự án     |
| priority    | String       | Độ ưu tiên: Thấp, cao,...|
| status      | String       | Trạng thái: hoàn thành, đang thực hiện,...|
| deadline    | Date         | Ngày kết thúc, hạn hoàn thành |

---

## 2. Project API
**GET /api/projects**
- Mô tả: Lấy danh sách tất cả dự án của user hiện tại.
- Response thành công (200 OK):
``` json
[
  {
    "id": "projec001",
    "userId": "user123",
    "title": "Project 1",
    "description": "Des project 1",
    "priority": "Trung bình",
    "status": "Đang thực hiện",
    "deadline": "29/06/2025"
  },
  {
    "id": "projec002",
    "userId": "user123",
    "title": "Project 2",
    "description": "Des project 2",
    "priority": "Cao",
    "status": "Đã hoàn thành",
    "deadline": "29/06/2025"
  }
]
```

---

**POST /api/projects**
- Mô tả: Tạo dự án mới.
- Request Body (JSON):
``` json
{
  "title": "Project mới",
  "description": "Mô tả project mới",
  "priority": "Cao",
  "status": "Lên kế hoạch",
  "deadline": "30/06/2025"
}
```
- Response thành công (201 Created):
``` json
{
  "id": "project003",
  "userId": "user123",
  "title": "Project mới",
  "description": "Mô tả project mới",
  "priority": "Cao",
  "status": "Lên kế hoạch",
  "deadline": "30/06/2025"e
}
```

---

**PUT /api/projects/{projectId}**
- Mô tả: Cập nhật dự án.
- Request Body (JSON):
``` json
{
  "title": "Cập nhật project",
  "description": "Mô tả cập nhật project",
  "priority": "Thấp",
  "status": "Lên kế hoạch",
  "deadline": "01/07/2025"
}
```
- Response thành công (200 OK):
``` json
{
  "id": "project003",
  "userId": "user123",
  "title": "Cập nhật project",
  "description": "Mô tả cập nhật project",
  "priority": "Thấp",
  "status": "Lên kế hoạch",
  "deadline": "01/07/2025"
}
```

---

**DELETE /api/project/{projectId}**
- Response thành công (204 No Content): Không trả về nội dung
