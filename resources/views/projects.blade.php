<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tạo dự án mới') }}
        </h2>
    </x-slot>
    <div class="container mx-auto max-w-lg p-6 bg-white rounded shadow">
        <form id="create-project-form">
            <div class="mb-4">
                <label class="block mb-1 font-semibold">Tên dự án</label>
                <input type="text" name="name" class="w-full border rounded px-3 py-2" required />
            </div>
            <div class="mb-4">
                <label class="block mb-1 font-semibold">Ngày hoàn thành</label>
                <input type="date" name="deadline" class="w-full border rounded px-3 py-2" required />
            </div>
            <div class="mb-4">
                <label class="block mb-1 font-semibold">Mức độ ưu tiên</label>
                <select name="priority" class="w-full border rounded px-3 py-2" required>
                    <option value="Thấp">Thấp</option>
                    <option value="Trung bình">Trung bình</option>
                    <option value="Cao">Cao</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block mb-1 font-semibold">Trạng thái</label>
                <select name="status" class="w-full border rounded px-3 py-2" required>
                    <option value="Lên kế hoạch">Lên kế hoạch</option>
                    <option value="Đang thực hiện">Đang thực hiện</option>
                    <option value="Đang xem xét">Đang xem xét</option>
                    <option value="Hoàn thành">Hoàn thành</option>
                </select>
            </div>
            <button type="submit" class="bg-blue-600 text-black px-4 py-2 rounded font-bold w-full border-2 border-black">+ Tạo</button>
        </form>
    </div>
    <script>
        document.getElementById('create-project-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            const form = e.target;
            const data = {
                title: form.name.value, // Đổi tên trường cho đúng với backend
                deadline: form.deadline.value,
                priority: form.priority.value,
                status: form.status.value
            };
            try {
                // Lấy CSRF cookie cho Sanctum
                await fetch('/sanctum/csrf-cookie', { credentials: 'same-origin' });
                const response = await fetch('/api/projects', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify(data)
                });
                if (response.ok) {
                    alert('Tạo dự án thành công!');
                    window.location.href = '/dashboard';
                } else {
                    const error = await response.json();
                    alert('Có lỗi xảy ra khi tạo dự án: ' + (error.message || 'Unknown error'));
                }
            } catch (err) {
                alert('Không thể kết nối tới server!');
            }
        });
    </script>
</x-app-layout>
