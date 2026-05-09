<?php
$title = '积分充值';
ob_start();
?>

<div class="flex justify-between flex-wrap items-center pt-3 pb-2 mb-4 border-b border-gray-200">
    <h1 class="text-2xl font-bold text-gray-800">积分充值</h1>
    <div class="flex items-center space-x-2">
        <span class="text-sm text-gray-500">当前积分：<span class="font-bold text-green-600 text-lg"><?php echo number_format($user['points'] ?? 0); ?></span></span>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    <?php if (empty($packages)): ?>
        <div class="col-span-full text-center py-12 text-gray-400">
            <i class="bi bi-gift text-6xl opacity-25"></i>
            <p class="mt-4">暂无充值套餐</p>
        </div>
    <?php else: ?>
        <?php foreach ($packages as $pkg): ?>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-300 relative overflow-hidden group">
                <!-- Header -->
                <div class="text-center mb-4">
                    <h5 class="text-xl font-bold text-gray-800 mb-2"><?php echo htmlspecialchars($pkg['name']); ?></h5>
                    
                    <div class="flex items-baseline justify-center text-indigo-600 mb-2">
                        <?php 
                        $totalPoints = $pkg['points'] + floor($pkg['points'] * ($pkg['bonus_percent'] / 100));
                        ?>
                        <span class="text-4xl font-extrabold tracking-tight"><?php echo number_format($totalPoints); ?></span>
                        <span class="ml-1 text-lg font-medium text-gray-500">积分</span>
                    </div>
                </div>

                <!-- Bonus Badge -->
                <div class="h-8 mb-4 flex justify-center items-center">
                    <?php if ($pkg['bonus_percent'] > 0): ?>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                            <i class="bi bi-stars mr-1"></i>赠送 <?php echo $pkg['bonus_percent']; ?>%
                        </span>
                    <?php endif; ?>
                </div>

                <!-- Price & Action -->
                <div class="text-center">
                    <h4 class="text-2xl font-bold text-red-500 mb-6">¥ <?php echo number_format($pkg['price'], 2); ?></h4>
                    
                    <button type="button" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-base font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors" onclick='openRechargeModal(<?php echo json_encode($pkg); ?>)'>
                        立即充值
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- History Section -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8">
    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
        <h5 class="text-lg font-bold text-gray-900 flex items-center">
            <i class="bi bi-clock-history mr-2 text-indigo-600"></i>最近充值记录
        </h5>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">时间</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">套餐</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">金额</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">积分</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">状态</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">备注</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($recentOrders)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500 italic">暂无充值记录</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($recentOrders as $order): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $order['created_at']; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($order['package_name']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold">¥<?php echo number_format($order['amount'], 2); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-indigo-600 font-bold">+<?php echo number_format($order['points']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if ($order['status'] === 'approved'): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">已完成</span>
                                <?php elseif ($order['status'] === 'rejected'): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">已拒绝</span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">审核中</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($order['admin_remark'] ?? '-'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Tailwind Modal -->
<div id="rechargeModal" class="relative z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Background backdrop -->
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity backdrop-blur-sm" onclick="closeRechargeModal()"></div>

    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <!-- Modal panel -->
            <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-4xl">
                
                <!-- Modal Header -->
                <div class="bg-gradient-to-r from-[#1a5f7a] to-[#2a7a9c] px-6 py-4 flex justify-between items-center text-white">
                    <div class="flex items-center gap-3">
                        <i class="bi bi-wallet2 text-2xl"></i>
                        <div>
                            <h3 class="text-xl font-bold" id="modal-title">付款方式指引</h3>
                            <p class="text-sm opacity-90 text-blue-100">选择适合您的付款方式，完成充值流程</p>
                        </div>
                    </div>
                    <button type="button" class="text-white hover:text-gray-200 transition-colors" onclick="closeRechargeModal()">
                        <i class="bi bi-x-lg text-2xl"></i>
                    </button>
                </div>

                <div class="px-6 py-6 max-h-[80vh] overflow-y-auto">
                    <!-- Selected Package Info -->
                    <div class="bg-indigo-50 border border-indigo-100 rounded-lg p-4 mb-6 flex justify-between items-center">
                        <div>
                            <span class="text-sm text-indigo-600 font-medium block mb-1">当前选择套餐</span>
                            <h4 class="text-xl font-bold text-gray-900" id="modal-pkg-name">套餐名称</h4>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-bold text-red-500" id="modal-pkg-price">¥ 0.00</p>
                            <p class="text-sm text-gray-500" id="modal-pkg-points">0 积分</p>
                        </div>
                    </div>

                    <!-- Payment Method Tabs -->
                    <div class="mb-6">
                        <div class="flex space-x-2 border-b border-gray-200" role="tablist">
                            <button class="px-4 py-2 text-sm font-medium text-blue-600 border-b-2 border-blue-600 focus:outline-none transition-colors flex items-center gap-2" id="tab-bank" onclick="switchTab('bank')">
                                <i class="bi bi-bank"></i> 银行对公汇款
                            </button>
                            <button class="px-4 py-2 text-sm font-medium text-gray-500 border-b-2 border-transparent hover:text-gray-700 hover:border-gray-300 focus:outline-none transition-colors flex items-center gap-2" id="tab-wechat" onclick="switchTab('wechat')">
                                <i class="bi bi-wechat"></i> 微信扫码支付
                            </button>
                            <button class="px-4 py-2 text-sm font-medium text-gray-500 border-b-2 border-transparent hover:text-gray-700 hover:border-gray-300 focus:outline-none transition-colors flex items-center gap-2" id="tab-alipay" onclick="switchTab('alipay')">
                                <i class="bi bi-alipay"></i> 支付宝支付
                            </button>
                        </div>

                        <!-- Bank Transfer Content -->
                        <div id="content-bank" class="py-4 animate-fade-in">
                            <div class="bg-blue-50/50 rounded-lg p-5 border border-blue-100 mb-6">
                                <h5 class="text-lg font-bold text-[#1a5f7a] mb-4 flex items-center gap-2">
                                    <i class="bi bi-check-circle-fill text-blue-600"></i> 三步完成充值
                                </h5>
                                <div class="space-y-4">
                                    <div class="flex gap-4">
                                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-[#2a7a9c] text-white flex items-center justify-center font-bold">1</div>
                                        <div>
                                            <strong class="text-gray-900 block mb-1">登录网上银行</strong>
                                            <p class="text-sm text-gray-600">使用您的企业网银账户登录网上银行系统，进入转账汇款功能。</p>
                                        </div>
                                    </div>
                                    <div class="flex gap-4">
                                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-[#2a7a9c] text-white flex items-center justify-center font-bold">2</div>
                                        <div>
                                            <strong class="text-gray-900 block mb-1">填写专属账户信息</strong>
                                            <p class="text-sm text-gray-600">转账时请填写下方显示的专属账户信息，确保信息准确无误。</p>
                                        </div>
                                    </div>
                                    <div class="flex gap-4">
                                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-[#2a7a9c] text-white flex items-center justify-center font-bold">3</div>
                                        <div>
                                            <strong class="text-gray-900 block mb-1">提交充值申请</strong>
                                            <p class="text-sm text-gray-600">转账完成后，请在下方填写备注并点击“提交申请”按钮。</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-[#f0f9ff] rounded-lg p-5 border border-[#d1e9ff]">
                                <p class="text-gray-800 font-bold mb-2">转账时请填写以下专属账户信息：</p>
                                <p class="text-sm text-gray-600 mb-4"><span class="bg-[#fff7e6] text-[#d46b08] px-1 rounded">户名、账号、支行、开户地为必填信息且不可填写错误</span></p>
                                
                                <div class="overflow-x-auto">
                                    <table class="w-full text-sm text-left">
                                        <tr class="border-b border-gray-200/50">
                                            <td class="py-2 text-[#1a5f7a] font-bold w-24">户名：</td>
                                            <td class="py-2 font-bold text-gray-900 copy-text cursor-pointer hover:text-blue-600 transition-colors" onclick="copyToClipboard(this)"><?php echo htmlspecialchars($settings['bank_account_name'] ?? '请联系管理员配置'); ?></td>
                                        </tr>
                                        <tr class="border-b border-gray-200/50">
                                            <td class="py-2 text-[#1a5f7a] font-bold">账号：</td>
                                            <td class="py-2 font-bold text-gray-900 copy-text cursor-pointer hover:text-blue-600 transition-colors" onclick="copyToClipboard(this)"><?php echo htmlspecialchars($settings['bank_account_number'] ?? '请联系管理员配置'); ?></td>
                                        </tr>
                                        <tr class="border-b border-gray-200/50">
                                            <td class="py-2 text-[#1a5f7a] font-bold">开户行：</td>
                                            <td class="py-2 font-bold text-gray-900 copy-text cursor-pointer hover:text-blue-600 transition-colors" onclick="copyToClipboard(this)"><?php echo htmlspecialchars($settings['bank_name'] ?? '请联系管理员配置'); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="py-2 text-[#1a5f7a] font-bold">备注：</td>
                                            <td class="py-2"><span class="bg-[#fff7e6] text-[#d46b08] px-2 py-0.5 rounded font-medium">填写您的手机号</span>（非常重要，用于核对汇款信息）</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- WeChat Pay Content -->
                        <div id="content-wechat" class="hidden py-4 animate-fade-in">
                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <h5 class="text-lg font-bold text-[#07c160] mb-4 flex items-center gap-2">
                                        <i class="bi bi-wechat"></i> 微信扫码快捷支付
                                    </h5>
                                    <div class="space-y-6">
                                        <div class="flex gap-4">
                                            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-[#07c160] text-white flex items-center justify-center font-bold">1</div>
                                            <div>
                                                <strong class="text-gray-900 block mb-1">打开微信扫一扫</strong>
                                                <p class="text-sm text-gray-600">使用微信扫一扫功能扫描右侧收款码。</p>
                                            </div>
                                        </div>
                                        <div class="flex gap-4">
                                            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-[#07c160] text-white flex items-center justify-center font-bold">2</div>
                                            <div>
                                                <strong class="text-gray-900 block mb-1">输入付款金额</strong>
                                                <p class="text-sm text-gray-600">请输入金额：<span class="text-red-500 font-bold text-lg" id="wechat-amount">¥ 0.00</span></p>
                                            </div>
                                        </div>
                                        <div class="flex gap-4">
                                            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-[#07c160] text-white flex items-center justify-center font-bold">3</div>
                                            <div>
                                                <strong class="text-gray-900 block mb-1">添加付款备注</strong>
                                                <p class="text-sm text-gray-600">付款时请务必备注：<span class="bg-[#fff7e6] text-[#d46b08] px-1 rounded font-medium">公司名称 + 手机号</span></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex flex-col items-center justify-center bg-gray-50 rounded-lg p-6 border border-gray-200">
                                    <div class="bg-white p-2 rounded shadow-sm border border-gray-200 mb-3">
                                        <?php if (!empty($settings['wechat_pay_image'])): ?>
                                            <img src="<?php echo htmlspecialchars($settings['wechat_pay_image']); ?>" alt="微信收款二维码" class="w-48 h-48 object-cover cursor-pointer hover:opacity-90 transition-opacity" onclick="showImageModal(this.src)">
                                        <?php else: ?>
                                            <div class="w-48 h-48 bg-gray-100 flex items-center justify-center text-gray-400 text-sm">
                                                暂未配置二维码
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <p class="text-sm text-gray-500"><i class="bi bi-scan me-1"></i>使用微信扫一扫支付</p>
                                </div>
                            </div>
                        </div>

                        <!-- Alipay Content -->
                        <div id="content-alipay" class="hidden py-4 animate-fade-in">
                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <h5 class="text-lg font-bold text-[#1677ff] mb-4 flex items-center gap-2">
                                        <i class="bi bi-alipay"></i> 支付宝扫码支付
                                    </h5>
                                    <div class="space-y-6">
                                        <div class="flex gap-4">
                                            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-[#1677ff] text-white flex items-center justify-center font-bold">1</div>
                                            <div>
                                                <strong class="text-gray-900 block mb-1">打开支付宝扫一扫</strong>
                                                <p class="text-sm text-gray-600">使用支付宝扫一扫功能扫描右侧收款码。</p>
                                            </div>
                                        </div>
                                        <div class="flex gap-4">
                                            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-[#1677ff] text-white flex items-center justify-center font-bold">2</div>
                                            <div>
                                                <strong class="text-gray-900 block mb-1">输入付款金额</strong>
                                                <p class="text-sm text-gray-600">请输入金额：<span class="text-red-500 font-bold text-lg" id="alipay-amount">¥ 0.00</span></p>
                                            </div>
                                        </div>
                                        <div class="flex gap-4">
                                            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-[#1677ff] text-white flex items-center justify-center font-bold">3</div>
                                            <div>
                                                <strong class="text-gray-900 block mb-1">添加付款备注</strong>
                                                <p class="text-sm text-gray-600">付款时请务必备注：<span class="bg-[#fff7e6] text-[#d46b08] px-1 rounded font-medium">公司名称 + 手机号</span></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex flex-col items-center justify-center bg-gray-50 rounded-lg p-6 border border-gray-200">
                                    <div class="bg-white p-2 rounded shadow-sm border border-gray-200 mb-3">
                                        <?php if (!empty($settings['alipay_image'])): ?>
                                            <img src="<?php echo htmlspecialchars($settings['alipay_image']); ?>" alt="支付宝收款二维码" class="w-48 h-48 object-cover cursor-pointer hover:opacity-90 transition-opacity" onclick="showImageModal(this.src)">
                                        <?php else: ?>
                                            <div class="w-48 h-48 bg-gray-100 flex items-center justify-center text-gray-400 text-sm">
                                                暂未配置二维码
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <p class="text-sm text-gray-500"><i class="bi bi-scan me-1"></i>使用支付宝扫一扫支付</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Completed / Contact -->
                    <div class="bg-gray-50 rounded-lg p-5 border border-gray-200 mb-6">
                        <h6 class="font-bold text-gray-900 mb-4 flex items-center">
                            <i class="bi bi-chat-dots-fill text-indigo-500 me-2"></i> 付款完成后
                        </h6>
                        <div class="flex flex-col md:flex-row gap-6 items-center">
                            <div class="flex-shrink-0">
                                <?php if (!empty($settings['contact_manager_qr'])): ?>
                                    <img src="<?php echo htmlspecialchars($settings['contact_manager_qr']); ?>" alt="业务经理微信" class="w-24 h-24 object-cover rounded border border-gray-200 cursor-pointer" onclick="showImageModal(this.src)">
                                <?php else: ?>
                                    <div class="w-24 h-24 bg-gray-200 rounded flex items-center justify-center text-xs text-gray-500">经理微信</div>
                                <?php endif; ?>
                            </div>
                            <div class="flex-1 text-sm text-gray-600">
                                <p class="mb-2">无论使用哪种付款方式，完成支付后请务必添加业务经理微信，发送付款凭证以便我们快速为您处理充值业务。</p>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-2">
                                    <div class="bg-white px-3 py-2 rounded border border-gray-200">
                                        <span class="text-gray-500">业务经理微信：</span>
                                        <span class="font-bold text-gray-900 select-all"><?php echo htmlspecialchars($settings['contact_manager_phone'] ?? '18602233021'); ?></span>
                                    </div>
                                    <div class="bg-white px-3 py-2 rounded border border-gray-200">
                                        <span class="text-gray-500">服务热线：</span>
                                        <a href="tel:<?php echo htmlspecialchars($settings['contact_manager_phone'] ?? '18602233021'); ?>" class="font-bold text-indigo-600 hover:underline"><?php echo htmlspecialchars($settings['contact_manager_phone'] ?? '18602233021'); ?></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submission Form -->
                    <div class="border-t border-gray-200 pt-6">
                        <h6 class="font-bold text-gray-900 mb-4">提交充值申请</h6>
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label for="modal-remark" class="block text-sm font-medium text-gray-700 mb-1">备注信息 <span class="text-red-500">*</span></label>
                                <textarea id="modal-remark" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md p-3 border" rows="3" placeholder="请填写您的充值账号、付款方式（银行/微信/支付宝）及付款人姓名，方便管理员核对。"></textarea>
                            </div>
                            <input type="hidden" id="modal-pkg-id">
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="bg-gray-50 px-6 py-4 sm:flex sm:flex-row-reverse border-t border-gray-200">
                    <button type="button" class="inline-flex w-full justify-center rounded-md bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 sm:ml-3 sm:w-auto transition-colors items-center" onclick="submitOrder()" id="btn-submit">
                        <i class="bi bi-check2-circle me-2"></i> 提交申请
                    </button>
                    <button type="button" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-4 py-2.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto transition-colors" onclick="closeRechargeModal()">
                        取消
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Image Preview Modal -->
<div id="imageModal" class="relative z-[60] hidden" aria-labelledby="image-modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-black bg-opacity-90 transition-opacity backdrop-blur-sm" onclick="closeImageModal()"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative max-w-3xl w-full">
                <button type="button" class="absolute -top-12 right-0 text-white hover:text-gray-300 text-3xl" onclick="closeImageModal()">
                    <i class="bi bi-x-lg"></i>
                </button>
                <img id="preview-image" src="" alt="Preview" class="w-full h-auto rounded-lg shadow-2xl">
            </div>
        </div>
    </div>
</div>

<script>
function openRechargeModal(pkg) {
    document.getElementById('modal-pkg-id').value = pkg.id;
    document.getElementById('modal-pkg-name').textContent = pkg.name;
    document.getElementById('modal-remark').value = '';
    
    const price = parseFloat(pkg.price).toFixed(2);
    const totalPoints = Math.floor(pkg.points * (1 + pkg.bonus_percent / 100));
    
    document.getElementById('modal-pkg-price').textContent = '¥ ' + price;
    document.getElementById('modal-pkg-points').textContent = totalPoints.toLocaleString() + ' 积分';
    
    // Update amounts in payment tabs
    document.getElementById('wechat-amount').textContent = '¥ ' + price;
    document.getElementById('alipay-amount').textContent = '¥ ' + price;
        
    document.getElementById('rechargeModal').classList.remove('hidden');
    
    // Default to bank tab
    switchTab('bank');
}

function closeRechargeModal() {
    document.getElementById('rechargeModal').classList.add('hidden');
}

function switchTab(tabName) {
    // Reset tabs
    ['bank', 'wechat', 'alipay'].forEach(t => {
        const btn = document.getElementById('tab-' + t);
        const content = document.getElementById('content-' + t);
        
        if (t === tabName) {
            btn.classList.remove('text-gray-500', 'border-transparent');
            btn.classList.add('text-blue-600', 'border-blue-600');
            content.classList.remove('hidden');
        } else {
            btn.classList.add('text-gray-500', 'border-transparent');
            btn.classList.remove('text-blue-600', 'border-blue-600');
            content.classList.add('hidden');
        }
    });
}

function showImageModal(src) {
    document.getElementById('preview-image').src = src;
    document.getElementById('imageModal').classList.remove('hidden');
}

function closeImageModal() {
    document.getElementById('imageModal').classList.add('hidden');
}

function copyToClipboard(element) {
    const text = element.innerText;
    navigator.clipboard.writeText(text).then(() => {
        const originalText = element.innerText;
        // Optional: show tooltip or visual feedback
        const originalColor = element.className;
        element.classList.remove('text-gray-900');
        element.classList.add('text-green-600');
        
        // Use a temporary span to show "Copied" if needed, or just flash color
        // Simple flash
        setTimeout(() => {
            element.classList.remove('text-green-600');
            element.classList.add('text-gray-900');
        }, 500);
        
        alert('已复制: ' + text);
    });
}

function submitOrder() {
    const pkgId = document.getElementById('modal-pkg-id').value;
    const remark = document.getElementById('modal-remark').value;
    const btn = document.getElementById('btn-submit');
    const originalText = btn.innerHTML;
    
    if (!remark.trim()) {
        alert('请填写备注信息');
        return;
    }
    
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin mr-2"></i>提交中...';
    
    fetch('/user/recharge/create', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            package_id: pkgId,
            remark: remark,
            csrf_token: '<?php echo $_SESSION['csrf_token'] ?? ''; ?>'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.message || '提交失败');
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('系统错误，请重试');
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
}
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>
