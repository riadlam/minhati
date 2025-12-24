<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>إعادة تعيين كلمة المرور</title>
</head>
<body dir="rtl">
    <h2>طلب إعادة تعيين كلمة المرور</h2>
    <p>لقد طلبت إعادة تعيين كلمة المرور الخاصة بك.</p>
    <p>اضغط على الرابط أدناه لإعادة التعيين:</p>
    <a href="{{ url('/reset-password/' . $token) }}">إعادة تعيين كلمة المرور</a>
    <p>إذا لم تطلب ذلك، يمكنك تجاهل هذه الرسالة.</p>
</body>
</html>
