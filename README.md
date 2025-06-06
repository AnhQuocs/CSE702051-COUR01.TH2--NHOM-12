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
| deadline    | Datetime     | Ngày kết thúc, hạn hoàn thành |

---

## 2. Authentication API
### POST /api/auth/logIn
**Mô tả:** Đăng nhập user, trả về token để xác thực các request tiếp theo.
- Request Body:
``` json
{
  "username": "user1",
  "password": "pass123"
}
```
---
- Response thành công (200 OK)
``` json
{
  "accessToken": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "refreshToken": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "id": "user123...",
  "email": "user1@example.com",
  "username": "User1"
}
```
- Response lỗi (ví dụ: sai Email hoặc mật khẩu):
``` json
{
  "error": "Incorrect email or password"
}
```

---

### POST /api/auth/logOut
**Mô tả: Đăng xuất user, invalid token.**
- Headers:
``` makefile
Authorization: Bearer <token>
```
- Response thành công (200 OK):
``` json
{
  "message": "Logged out successfully"
}
```
