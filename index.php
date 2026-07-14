<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> مكتب سمارت باور للطاقة الشمسية والانظمة الذكية بإدارة/ذوالفقار شروان الخزعلي</title>
    <!-- استدعاء مكتبة Tailwind CSS للتصميم السريع -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* ستايل مخصص لعملية الطباعة لكي نطبع الفاتورة فقط ونخفي لوحة التحكم */
        @media print {
            body {
                background-color: white !important;
            }
            body * {
                visibility: hidden;
            }
            #invoice-preview, #invoice-preview * {
                visibility: visible;
            }
            #invoice-preview {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                box-shadow: none !important;
                border: none !important;
            }
        }
    </style>
</head>
<body class="bg-gray-100 font-sans min-h-screen pb-12">

    <header class="bg-blue-600 text-white p-4 shadow-md mb-6">
        <h1 class="text-2xl font-bold text-center">Smart Power</h1>
    </header>

    <div class="max-w-7xl mx-auto px-4 grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- ================= الجانب الأيمن: لوحة إدخال البيانات (التصميم الداكن الاحترافي) ================= -->
        <div class="lg:col-span-5 bg-slate-800 p-6 rounded-lg shadow-xl space-y-6 text-white border border-slate-700 h-fit">
            
            <!-- أولاً: بيانات المشتري -->
            <div>
                <h3 class="text-lg font-bold text-blue-400 border-b border-slate-700 pb-2 mb-4">بيانات المشتري</h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm text-slate-300 mb-1">اسم المشتري</label>
                        <input type="text" id="buyer_name" class="w-full p-2 bg-slate-700 text-white border border-slate-600 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none placeholder-slate-400" placeholder="مثال: علي جبار">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-sm text-slate-300 mb-1">رقم الهاتف</label>
                            <input type="text" id="buyer_phone" class="w-full p-2 bg-slate-700 text-white border border-slate-600 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none placeholder-slate-400" placeholder="077xxxxxxx">
                        </div>
                        <div>
                            <label class="block text-sm text-slate-300 mb-1">العنوان</label>
                            <input type="text" id="buyer_address" class="w-full p-2 bg-slate-700 text-white border border-slate-600 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none placeholder-slate-400" placeholder="بغداد، المنصور">
                        </div>
                    </div>
                </div>
            </div>

            <!-- ثانياً: إدخال المواد -->
            <div>
                <h3 class="text-lg font-bold text-blue-400 border-b border-slate-700 pb-2 mb-4">إضافة المواد للفاتورة</h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm text-slate-300 mb-1">اسم المادة</label>
                        <input type="text" id="item_name" class="w-full p-2 bg-slate-700 text-white border border-slate-600 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-sm text-slate-300 mb-1">السعر مفرد (د.ع)</label>
                            <input type="number" id="item_price" class="w-full p-2 bg-slate-700 text-white border border-slate-600 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none placeholder-slate-400" placeholder="مثال: 25000">
                        </div>
                        <div>
                            <label class="block text-sm text-slate-300 mb-1">الرقم التسلسلي (S/N)</label>
                            <input type="text" id="item_sn" class="w-full p-2 bg-slate-700 text-white border border-slate-600 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm text-slate-300 mb-1">المواصفات</label>
                        <textarea id="item_spec" rows="2" class="w-full p-2 bg-slate-700 text-white border border-slate-600 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none"></textarea>
                    </div>
                    
                    <!-- عداد المواد (الكمية) -->
                    <div>
                        <label class="block text-sm text-slate-300 mb-1">الكمية (العدد)</label>
                        <div class="flex items-center">
                            <button onclick="decrementQty()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-r font-bold text-lg transition-colors">-</button>
                            <input type="number" id="item_qty" value="1" min="1" class="w-20 p-2 text-center bg-slate-700 text-white border-t border-b border-slate-600 outline-none font-bold" readonly>
                            <button onclick="incrementQty()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-l font-bold text-lg transition-colors">+</button>
                        </div>
                    </div>

                    <button onclick="addItemToInvoice()" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded font-bold transition-all shadow-md transform active:scale-95">
                        إضافة المادة للفاتورة ↩
                    </button>
                </div>
            </div>

            <!-- أزرار العمليات الكبرى -->
            <div class="border-t border-slate-700 pt-4 grid grid-cols-2 gap-3">
                <button onclick="printInvoice()" class="bg-emerald-600 hover:bg-emerald-700 text-white py-3 rounded-lg font-bold flex items-center justify-center gap-2 transition-colors shadow-md">
                    🖨️ طباعة الفاتورة
                </button>
                <button onclick="saveAndExportPDF()" class="bg-violet-600 hover:bg-violet-700 text-white py-3 rounded-lg font-bold flex items-center justify-center gap-2 transition-colors shadow-md">
                    💾 حفظ وتصدير PDF
                </button>
            </div>

        </div>

        <!-- ================= الجانب الأيسر: المعاينة التلقائية الحية (الوصل الفاتح) ================= -->
        <div class="lg:col-span-7">
            <div id="invoice-preview" class="bg-white p-8 rounded-lg shadow-md border border-gray-200 min-h-[842px] flex flex-col justify-between">
                
                <!-- هيدر الفاتورة المعاينة -->
                <div>
                    <div class="flex justify-between items-center border-b pb-4 mb-6">
                        
                        <!-- الجانب الأيمن للفاتورة: معلومات الوصل -->
                        <div class="text-right w-1/3">
                            <h3 class="text-2xl font-bold text-gray-800">فاتورة مبيعات</h3>
                            <p class="text-xs text-gray-500 mt-1">التاريخ: <span id="invoice_date">-- / -- / ----</span></p>
                            <p class="text-xs text-gray-500">الرقم المرجعي: <span id="invoice_ref" class="font-mono font-bold">DRAFT</span></p>
                        </div>

                        <!-- المنتصف: اللوغو -->
                        <div class="flex justify-center items-center w-1/3">
                            <img src="logo.jpg" alt="Smart Power Logo" class="h-20 w-auto object-contain">
                        </div>

                        <!-- الجانب الأيسر للفاتورة: اسم الشركة والنشاط -->
                        <div class="text-left w-1/3">
                            <h2 class="text-3xl font-bold text-blue-700">شركة سمارت باور</h2>
                            <p class="text-sm text-gray-500 mt-1">للطاقة الشمسية والأنظمة الذكية</p>
                        </div>

                    </div>

                    <!-- معلومات المشتري المعروضة في الفاتورة -->
                    <div class="grid grid-cols-3 gap-4 bg-gray-50 p-4 rounded mb-6 text-sm">
                        <div>
                            <span class="text-gray-500 block">المشتري:</span>
                            <strong id="preview_buyer_name" class="text-gray-800">-</strong>
                        </div>
                        <div>
                            <span class="text-gray-500 block">الهاتف:</span>
                            <strong id="preview_buyer_phone" class="text-gray-800">-</strong>
                        </div>
                        <div>
                            <span class="text-gray-500 block">العنوان:</span>
                            <strong id="preview_buyer_address" class="text-gray-800">-</strong>
                        </div>
                    </div>

                    <!-- جدول المواد المعروض في الفاتورة -->
                    <table class="w-full text-right border-collapse mb-6 text-sm">
                        <thead>
                            <tr class="bg-gray-100 border-b">
                                <th class="p-2 text-gray-600 font-semibold">المادة والمواصفات / S/N</th>
                                <th class="p-2 text-gray-600 font-semibold text-center w-16">الكمية</th>
                                <th class="p-2 text-gray-600 font-semibold text-left w-24">سعر المفرد</th>
                                <th class="p-2 text-gray-600 font-semibold text-left w-24">الإجمالي</th>
                                <th class="p-2 text-gray-600 font-semibold text-center w-12 print:hidden">حذف</th>
                            </tr>
                        </thead>
                        <tbody id="invoice_items_table">
                            <!-- سيتم إدراج المواد هنا تلقائياً بواسطة جافا سكريبت -->
                        </tbody>
                    </table>
                </div>

                <!-- إجماليات الفاتورة بالأسفل -->
                <div class="border-t pt-4">
                    <div class="flex justify-between items-center text-lg">
                        <span class="font-bold text-gray-700">المجموع الكلي:</span>
                        <span class="font-bold text-blue-700 text-2xl" id="preview_total_amount">0 د.ع</span>
                    </div>
                    <div class="text-center text-xs text-gray-400 mt-8 border-t pt-4">
                        العنوان: الديوانية / ساحة الاحتفالات - قرب مطعم دجاج كهرمانة 07778777937 - 07818000277
                    </div>
                </div>

            </div>
        </div>

    </div>

    <!-- استدعاء كود البرمجة التفاعلية لجعل الصفحة حية -->
    <script src="app.js"></script>
</body>
</html>