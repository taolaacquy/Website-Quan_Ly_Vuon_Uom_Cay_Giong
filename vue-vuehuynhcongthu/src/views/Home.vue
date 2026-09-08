<template>
  <main class="home-page">
    <header class="site-header">
      <router-link to="/home" class="brand" aria-label="Vườn Ươm Xanh">
        <span class="brand-mark" aria-hidden="true">
          <svg viewBox="0 0 64 64" fill="none"><path d="M31.9 55.4V29.8" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" /><path d="M31.8 37.2C21.1 36.5 14.1 29 14.5 17.4c10.6-.2 17.7 6.8 17.3 19.8Z" fill="currentColor" opacity=".9" /><path d="M32.3 31.3C34.1 19.4 41.7 13.2 52 12.6c.6 11.1-6.1 18.1-19.7 18.7Z" fill="currentColor" opacity=".68" /><path d="M19 55.5h27" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" /></svg>
        </span>
        <span>Vườn Ươm Xanh</span>
      </router-link>
      <div class="header-actions">
        <span class="welcome">Xin chào, <strong>{{ user?.fullname || 'Người bạn xanh' }}</strong></span>
        <button ref="cartButton" class="cart-button" aria-label="Mở giỏ hàng" @click="isCartOpen = true">Giỏ hàng <span>{{ cartItemCount }}</span></button>
        <button class="logout" @click="logout">Đăng xuất</button>
      </div>
    </header>

    <section class="hero">
      <div class="hero-content">
        <p class="eyebrow">Vườn giống tuyển chọn</p>
        <h1>Gieo mầm hôm nay,<br /><em>xanh mãi ngày mai.</em></h1>
        <p>Khám phá những giống cây khỏe mạnh, được tuyển chọn kỹ lưỡng để bắt đầu khu vườn của riêng bạn.</p>
        <a href="#catalog" class="explore">Khám phá cây giống <span aria-hidden="true">↓</span></a>
      </div>
      <div class="hero-note"><span>01</span><p>Một hạt giống,<br />triệu nền xanh!</p></div>
    </section>

    <section id="catalog" class="catalog">
      <div class="section-heading">
        <div><p class="eyebrow">Bộ sưu tập cây giống</p><h2>Chọn mầm xanh phù hợp</h2></div>
        <button class="catalog-cart" @click="isCartOpen = true" aria-label="Xem giỏ hàng">
          <span class="catalog-cart-icon">🛒</span><span><small>Giỏ cây của bạn</small><strong>{{ cartItemCount }} cây · {{ formatCurrency(cartTotal) }}</strong></span><b>→</b>
        </button>
      </div>

      <div class="filter-bar" aria-label="Lọc theo nhóm cây">
        <button v-for="category in categories" :key="category" :class="{ active: activeCategory === category }" @click="activeCategory = category">{{ category }}</button>
      </div>

      <div class="product-grid">
        <article v-for="(plant, index) in filteredPlants" :key="plant.name" class="plant-card" :class="{ featured: index === 0 }">
          <div class="plant-image" :style="{ backgroundImage: `url(${plant.image})` }">
            <span class="plant-type">{{ plant.category }}</span>
            <span class="plant-number">0{{ plants.indexOf(plant) + 1 }}</span>
          </div>
          <div class="plant-info">
            <p>{{ plant.latin }}</p>
            <h3>{{ plant.name }}</h3>
            <div class="product-panels">
              <details class="detail-panel">
                <summary>Chi tiết <span>+</span></summary>
                <p>{{ plant.description }}</p>
                <ul>
                  <li><b>Đặc tính:</b> {{ plant.note }}</li>
                  <li><b>Chăm sóc:</b> {{ plant.care[0] }}</li>
                  <li><b>Trưởng thành:</b> {{ growthFor(plant) }}</li>
                  <li><b>Công dụng:</b> {{ usageFor(plant) }}</li>
                </ul>
                <button class="more-detail" @click="openPlant(plant)">Xem hướng dẫn đầy đủ →</button>
              </details>
              <div class="price-panel">
                <small>Giá cây giống</small>
                <strong>{{ formatCurrency(priceFor(plant).amount) }}</strong>
                <div class="card-quantity" aria-label="Chọn số lượng">
                  <button :aria-label="`Giảm số lượng ${plant.name}`" @click="changeSelectedQuantity(plant.name, -1)">−</button><span>{{ selectedQuantity(plant.name) }}</span><button :aria-label="`Tăng số lượng ${plant.name}`" @click="changeSelectedQuantity(plant.name, 1)">+</button>
                </div>
                <button class="add-cart" @click="addSelectedToCart(plant, $event)">Thêm vào giỏ <span>+</span></button>
              </div>
            </div>
          </div>
        </article>
      </div>
    </section>

    <Teleport to="body">
      <div v-if="selectedPlant" class="plant-modal" role="dialog" aria-modal="true" :aria-label="`Thông tin ${selectedPlant.name}`" @click.self="closePlant">
        <div class="modal-card">
          <button class="close-modal" aria-label="Đóng thông tin cây" @click="closePlant">×</button>
          <div class="modal-image" :style="{ backgroundImage: `url(${selectedPlant.image})` }">
            <span>{{ selectedPlant.category }}</span>
          </div>
          <div class="modal-content">
            <p class="modal-latin">{{ selectedPlant.latin }}</p>
            <h2>{{ selectedPlant.name }}</h2>
            <p class="description">{{ selectedPlant.description }}</p>
            <div class="price-row"><div><span>Giá cây giống tham khảo</span><strong>{{ formatCurrency(priceFor(selectedPlant).amount) }}<small>/ cây</small></strong></div><button @click="addToCart(selectedPlant)">Thêm vào giỏ <span>+</span></button></div>
            <div class="guide-grid">
              <section>
                <span class="guide-icon">01</span>
                <h3>Hướng dẫn trồng</h3>
                <ul><li v-for="item in selectedPlant.planting" :key="item">{{ item }}</li></ul>
              </section>
              <section>
                <span class="guide-icon">02</span>
                <h3>Chăm sóc</h3>
                <ul><li v-for="item in selectedPlant.care" :key="item">{{ item }}</li></ul>
              </section>
              <section>
                <span class="guide-icon">03</span>
                <h3>Đặc tính & trưởng thành</h3>
                <ul><li>{{ selectedPlant.note }}</li><li>{{ growthFor(selectedPlant) }}</li></ul>
              </section>
              <section>
                <span class="guide-icon">04</span>
                <h3>Công dụng</h3>
                <ul><li>{{ usageFor(selectedPlant) }}</li></ul>
              </section>
            </div>
            <div class="source-links"><a class="image-source" :href="selectedPlant.imageSource" target="_blank" rel="noopener">Nguồn ảnh &amp; định danh cây ↗</a><a class="image-source" :href="priceFor(selectedPlant).source" target="_blank" rel="noopener">Nguồn giá tham khảo ↗</a></div>
          </div>
        </div>
      </div>
    </Teleport>

    <Teleport to="body">
      <div v-if="isCartOpen" class="cart-overlay" @click.self="isCartOpen = false">
        <aside class="cart-drawer" aria-label="Giỏ hàng">
          <div class="cart-header"><div><p class="eyebrow">Đơn hàng của bạn</p><h2>Giỏ cây giống</h2></div><button aria-label="Đóng giỏ hàng" @click="isCartOpen = false">×</button></div>
          <div v-if="cart.length" class="cart-items"><article v-for="item in cart" :key="item.plant.name" class="cart-item"><span class="cart-thumb" :style="{ backgroundImage: `url(${item.plant.image})` }"></span><div class="cart-item-info"><strong>{{ item.plant.name }}</strong><small>{{ formatCurrency(priceFor(item.plant).amount) }} / cây</small><div class="quantity"><button @click="updateQuantity(item.plant.name, item.quantity - 1)">−</button><span>{{ item.quantity }}</span><button @click="updateQuantity(item.plant.name, item.quantity + 1)">+</button></div></div><button class="remove-item" :aria-label="`Xóa ${item.plant.name}`" @click="removeFromCart(item.plant.name)">×</button></article></div>
          <div v-else class="empty-cart"><span>🌱</span><h3>Giỏ hàng đang trống</h3><p>Chọn những cây giống bạn muốn gieo trồng.</p></div>
          <div v-if="cart.length" class="checkout"><div class="subtotal"><span>Tạm tính ({{ cartItemCount }} cây)</span><strong>{{ formatCurrency(cartTotal) }}</strong></div><p>Phí vận chuyển sẽ được xác nhận theo địa chỉ nhận hàng.</p><button @click="isCheckoutOpen = true">Tiến hành thanh toán <span>→</span></button></div>
        </aside>
      </div>
    </Teleport>

    <Teleport to="body">
      <div v-if="isCheckoutOpen" class="checkout-overlay" @click.self="isCheckoutOpen = false">
        <section class="checkout-card" aria-label="Thanh toán chuyển khoản">
          <button class="close-checkout" aria-label="Đóng thanh toán" @click="isCheckoutOpen = false">×</button>
          <p class="eyebrow">Thanh toán chuyển khoản</p><h2>Hoàn tất đơn hàng</h2><p class="checkout-note">Vui lòng chuyển đúng nội dung để chủ vườn xác nhận đơn nhanh hơn.</p>
          <div class="bank-box"><span class="bank-icon">MB</span><div><small>Ngân hàng MB Bank</small><strong>HUYNH CONG THU</strong><b>0979745413</b></div><button @click="copyAccount">{{ copied ? 'Đã sao chép' : 'Sao chép' }}</button></div>
          <div class="transfer-total"><span>Tổng thanh toán</span><strong>{{ formatCurrency(cartTotal) }}</strong><small>Nội dung: VUX {{ orderCode }}</small></div>
          <label class="upload-label" for="payment-proof"><input id="payment-proof" type="file" accept="image/png,image/jpeg,image/webp" @change="handlePaymentProof" /><span class="upload-icon">↑</span><strong>{{ paymentProofName || 'Tải ảnh giao dịch thành công' }}</strong><small>Chấp nhận PNG, JPG hoặc WEBP</small></label>
          <img v-if="paymentProofUrl" class="payment-preview" :src="paymentProofUrl" alt="Ảnh giao dịch khách đã tải lên" />
          <button class="confirm-payment" :disabled="!paymentProofName" @click="confirmPayment">{{ paymentConfirmed ? 'Đã gửi yêu cầu xác nhận ✓' : 'Tôi đã chuyển khoản' }}</button>
          <p v-if="paymentConfirmed" class="payment-success">Cảm ơn bạn! Chủ vườn sẽ đối chiếu giao dịch và liên hệ xác nhận đơn.</p>
        </section>
      </div>
    </Teleport>

    <footer><span class="footer-mark">✦</span> Vườn Ươm Xanh · Nuôi dưỡng một Việt Nam xanh hơn</footer>
  </main>
</template>

<script setup lang="ts">
import { computed, onMounted, ref, watch } from 'vue'
import { useRouter } from 'vue-router'

type Plant = {
  name: string; category: string; latin: string; note: string; image: string; imageSource: string
  description: string; planting: string[]; care: string[]
}

const plants: Plant[] = [
  { name: 'Cây cam sành', category: 'Cây ăn quả', latin: 'Citrus reticulata × sinensis', note: 'Sai quả · Dễ chăm', image: 'https://commons.wikimedia.org/wiki/Special:FilePath/Citrus_reticulata.jpg?width=900', imageSource: 'https://commons.wikimedia.org/wiki/File:Citrus_reticulata.jpg', description: 'Cam sành là giống cây có múi quen thuộc tại Việt Nam, nổi bật với quả vỏ xanh sần, ruột cam đậm và vị ngọt chua hài hòa.', planting: ['Chọn nơi nắng tốt, đất tơi xốp và thoát nước.', 'Đặt bầu ngang mặt đất, lấp đất và cắm cọc cố định.', 'Tưới đẫm ngay sau trồng, phủ gốc bằng vật liệu hữu cơ.'], care: ['Giữ ẩm vừa phải; không để nước đọng quanh rễ.', 'Tỉa cành sâu bệnh, cành mọc trong tán để cây thông thoáng.', 'Theo dõi sâu vẽ bùa, rệp và dùng biện pháp phù hợp địa phương.'] },
  { name: 'Cây xoài keo', category: 'Cây ăn quả', latin: 'Mangifera indica', note: 'Năng suất cao', image: 'https://commons.wikimedia.org/wiki/Special:FilePath/Mangifera_indica_(367639315).jpg?width=900', imageSource: 'https://commons.wikimedia.org/wiki/File:Mangifera_indica_(367639315).jpg', description: 'Xoài keo thuộc loài xoài trồng, được ưa chuộng nhờ trái giòn, vị ngọt nhẹ và cây thích nghi tốt với khí hậu nhiệt đới.', planting: ['Chọn đất cao ráo, nhiều nắng và có lớp đất canh tác sâu.', 'Đào hố, trộn đất mặt với phân hữu cơ hoai mục trước khi đặt bầu.', 'Giữ cổ rễ cao hơn mặt đất, nén nhẹ và tưới sau trồng.'], care: ['Tạo tán sớm để cây thấp, tán đều và dễ thu hái.', 'Tưới bổ sung vào giai đoạn khô hạn, giảm tưới khi mưa kéo dài.', 'Vệ sinh vườn, thu gom quả rụng để hạn chế sâu bệnh.'] },
  { name: 'Cây mít Thái', category: 'Cây ăn quả', latin: 'Artocarpus heterophyllus', note: 'Quả to · Cơm dày', image: 'https://commons.wikimedia.org/wiki/Special:FilePath/JackfruitTree.jpg?width=900', imageSource: 'https://commons.wikimedia.org/wiki/File:JackfruitTree.jpg', description: 'Mít Thái là dòng mít trồng thuộc loài Artocarpus heterophyllus; cây sinh trưởng khỏe, cho quả lớn mọc trên thân và cành chính.', planting: ['Ưu tiên đất cao, thoát nước tốt; tránh vùng ngập úng.', 'Đặt cây vào đầu mùa mưa để giảm công tưới ban đầu.', 'Dùng cọc giữ cây non khi nơi trồng có gió mạnh.'], care: ['Tưới giữ ẩm ở giai đoạn kiến thiết, đặc biệt khi nắng kéo dài.', 'Tỉa bớt cành thấp, cành vô hiệu để tán thông thoáng.', 'Khi mang quả, chỉ giữ lượng quả phù hợp sức cây và chống đỡ cành.'] },
  { name: 'Cây bưởi da xanh', category: 'Cây ăn quả', latin: 'Citrus maxima', note: 'Thơm ngon · Ít hạt', image: 'https://commons.wikimedia.org/wiki/Special:FilePath/Citrus_maxima_(Burm.)_Osbeck_(3188650254).jpg?width=900', imageSource: 'https://commons.wikimedia.org/wiki/File:Citrus_maxima_(Burm.)_Osbeck_(3188650254).jpg', description: 'Bưởi da xanh là giống bưởi thuộc loài Citrus maxima, được yêu thích nhờ múi hồng, vị ngọt và hương thơm đặc trưng.', planting: ['Trồng nơi có nắng, đất giàu hữu cơ và thoát nước.', 'Đào hố rộng hơn bầu; không vùi sâu cổ rễ.', 'Phủ gốc nhưng chừa khoảng trống quanh thân để tránh ẩm quá mức.'], care: ['Tưới đều khi cây ra đọt non và nuôi quả.', 'Tỉa cành vượt, cành giao nhau để đón nắng trong tán.', 'Kiểm tra rệp, sâu hại và thoát nước thật nhanh sau mưa lớn.'] },
  { name: 'Cây keo', category: 'Cây lấy gỗ', latin: 'Acacia mangium', note: 'Sinh trưởng nhanh', image: 'https://ecozamba.org/wp-content/uploads/2024/06/acacia-mangium.jpg', imageSource: 'https://ecozamba.org/tout-savoir-sur-lacacia-mangium/', description: 'Keo tai tượng (Acacia mangium) là cây lâm nghiệp sinh trưởng nhanh, thường được trồng rừng sản xuất và phục hồi đất.', planting: ['Dọn thực bì vừa phải, giữ lại thảm thực vật cần thiết chống xói mòn.', 'Trồng cây vào đầu mùa mưa khi đất đủ ẩm.', 'Đặt bầu thẳng, lấp kín bầu và nén đất nhẹ quanh gốc.'], care: ['Phát dọn cỏ cạnh tranh trong những năm đầu.', 'Kiểm tra cây chết, dặm sớm để bảo đảm mật độ rừng.', 'Theo dõi sâu bệnh và nguy cơ cháy rừng vào mùa khô.'] },
  { name: 'Cây xà cừ', category: 'Cây lấy gỗ', latin: 'Khaya senegalensis', note: 'Tán rộng · Bền vững', image: 'https://commons.wikimedia.org/wiki/Special:FilePath/Khaya_senegalensis_01.jpg?width=900', imageSource: 'https://commons.wikimedia.org/wiki/File:Khaya_senegalensis_01.jpg', description: 'Xà cừ châu Phi (Khaya senegalensis) là cây gỗ lớn, tán rộng, được trồng lấy gỗ và tạo bóng mát ở vùng nhiệt đới.', planting: ['Chọn vị trí có không gian phát triển tán và rễ lâu dài.', 'Trồng vào đầu mùa mưa, làm bồn giữ nước quanh gốc.', 'Dùng cọc chống cho cây non tại nơi nhiều gió.'], care: ['Tưới hỗ trợ trong mùa khô của các năm đầu.', 'Tỉa cành thấp, chọn một thân chính để cây thẳng đẹp.', 'Không trồng sát công trình vì cây trưởng thành có tán và rễ lớn.'] },
  { name: 'Cây điều', category: 'Cây lấy gỗ', latin: 'Anacardium occidentale', note: 'Khỏe mạnh · Dễ trồng', image: 'https://static.inaturalist.org/photos/118369068/large.jpg', imageSource: 'https://www.inaturalist.org/taxa/122988-Anacardium-occidentale', description: 'Cây điều là cây nhiệt đới cho hạt và quả giả; tán cây khỏe, thích hợp nơi nhiều nắng và đất thoát nước.', planting: ['Chọn đất cao, thoát nước và có ánh sáng đầy đủ.', 'Bón lót hữu cơ hoai mục, đặt bầu thẳng rồi lấp đất.', 'Giữ khoảng cách trồng rộng để tán cây phát triển.'], care: ['Làm cỏ, phủ gốc và tưới hỗ trợ khi cây còn non.', 'Tạo tán thấp, tỉa cành sâu bệnh sau thu hoạch.', 'Theo dõi sâu bệnh trên chồi và hoa; ưu tiên quản lý tổng hợp.'] },
  { name: 'Cây mủ cao su', category: 'Cây công nghiệp', latin: 'Hevea brasiliensis', note: 'Giống chuẩn · Khỏe', image: 'https://commons.wikimedia.org/wiki/Special:FilePath/Rubber_Plantation.jpg?width=900', imageSource: 'https://commons.wikimedia.org/wiki/File:Rubber_Plantation.jpg', description: 'Cao su là cây công nghiệp lâu năm cho mủ latex; cây phù hợp vùng nhiệt đới có mùa mưa và điều kiện đất thoát nước.', planting: ['Chỉ chọn vùng phù hợp quy hoạch, khí hậu và nguồn giống được khuyến nghị.', 'Trồng đầu mùa mưa trên đất đã chuẩn bị, không để rễ cong.', 'Giữ hàng cây thẳng, đánh dấu để tiện chăm sóc về sau.'], care: ['Làm cỏ, vun gốc và bảo vệ cây non trong thời kỳ kiến thiết.', 'Duy trì rãnh thoát nước; tránh để gốc bị úng kéo dài.', 'Tham khảo cán bộ kỹ thuật địa phương trước khi bón phân hoặc khai thác mủ.'] },
  { name: 'Cây cà phê', category: 'Cây công nghiệp', latin: 'Coffea canephora', note: 'Hạt thơm · Năng suất', image: 'https://commons.wikimedia.org/wiki/Special:FilePath/Coffea_canephora_2_at_Aanakkulam.jpg?width=900', imageSource: 'https://commons.wikimedia.org/wiki/File:Coffea_canephora_2_at_Aanakkulam.jpg', description: 'Cà phê vối (Coffea canephora/Robusta) là cây công nghiệp chủ lực, cho quả chín đỏ và hạt được chế biến thành cà phê.', planting: ['Chọn đất sâu, thoát nước tốt và có nguồn nước tưới chủ động.', 'Trồng khi đất đủ ẩm; che nắng nhẹ cho cây non nếu nắng gắt.', 'Thiết kế vườn có đường đi và thoát nước ngay từ đầu.'], care: ['Tưới đúng thời điểm, vừa đủ; dùng phủ gốc để giữ ẩm mùa khô.', 'Tỉa cành sâu bệnh, cành già và chồi vượt để thông tán.', 'Bón dinh dưỡng cân đối theo tuổi cây, đất và tư vấn kỹ thuật địa phương.'] },
  { name: 'Cây hồ tiêu', category: 'Cây công nghiệp', latin: 'Piper nigrum', note: 'Phù hợp khí hậu Việt', image: 'https://commons.wikimedia.org/wiki/Special:FilePath/Black_Pepper_Plant.jpg?width=900', imageSource: 'https://commons.wikimedia.org/wiki/File:Black_Pepper_Plant.jpg', description: 'Hồ tiêu (Piper nigrum) là cây dây leo lâu năm, cho chùm quả được thu hái và chế biến thành hạt tiêu.', planting: ['Chọn đất tơi xốp, thoát nước tốt và chuẩn bị trụ cho dây leo.', 'Trồng đầu mùa mưa, đặt hom/cây giống cạnh trụ và che nhẹ lúc đầu.', 'Không trồng ở vùng dễ đọng nước hoặc có tiền sử bệnh rễ nặng.'], care: ['Giữ ẩm vừa phải; đặc biệt không để úng ở vùng rễ.', 'Buộc, dẫn dây lên trụ và tỉa dây bệnh để vườn thoáng.', 'Kiểm tra thối rễ, vàng lá thường xuyên; xử lý theo hướng dẫn chuyên môn.'] }
]

const categories = ['Tất cả', 'Cây ăn quả', 'Cây lấy gỗ', 'Cây công nghiệp']
const productPricing: Record<string, { amount: number; source: string }> = {
  'Cây cam sành': { amount: 25000, source: 'https://hadicofoods.vn/p-cay-giong/cay-giong-159' },
  'Cây xoài keo': { amount: 30000, source: 'https://hadicofoods.vn/p-cay-giong/cay-giong-159' },
  'Cây mít Thái': { amount: 30000, source: 'https://hadicofoods.vn/p-cay-giong/cay-giong-159' },
  'Cây bưởi da xanh': { amount: 30000, source: 'https://hadicofoods.vn/p-cay-giong/cay-giong-159' },
  'Cây keo': { amount: 5000, source: 'https://langsontv.vn/news/452/85025/gia-keo-giong-tang-cao' },
  'Cây xà cừ': { amount: 15000, source: 'https://giongcaytrong.org/cay-trong-lay-go/cay-giong-xa-cu.html' },
  'Cây điều': { amount: 40000, source: 'https://dongthanhcong.vn/' },
  'Cây mủ cao su': { amount: 16000, source: 'https://giongcaosu.com/gia-cay-giong-cao-su-tai-canh-trong-moi/' },
  'Cây cà phê': { amount: 8000, source: 'https://dantocphattrien.vietnamnet.vn/go-nut-that-ve-nguon-cay-giong-ca-phe-cho-ba-con-vung-dong-bao-dtts-1777277777334.htm' },
  'Cây hồ tiêu': { amount: 35000, source: 'https://shopee.vn/C%C3%A2y-gi%E1%BB%91ng-h%E1%BB%93-ti%C3%AAu-c%C3%A2y-ti%C3%AAu-gi%E1%BB%91ng-si%C3%AAu-tr%C3%A1i-%28c%C3%A2y-gi%E1%BB%91ng-chi-l%C4%83ng-47%29-i.983736695.22738253712' }
}
const activeCategory = ref('Tất cả')
const filteredPlants = computed(() => activeCategory.value === 'Tất cả' ? plants : plants.filter(plant => plant.category === activeCategory.value))
const selectedPlant = ref<Plant | null>(null)
type CartItem = { plant: Plant; quantity: number }
const cart = ref<CartItem[]>([])
const quantities = ref<Record<string, number>>({})
const cartButton = ref<HTMLElement | null>(null)
const isCartOpen = ref(false)
const isCheckoutOpen = ref(false)
const paymentProofName = ref('')
const paymentProofUrl = ref('')
const paymentConfirmed = ref(false)
const copied = ref(false)
const orderCode = `VX${String(Date.now()).slice(-6)}`
const user = ref<{ fullname?: string } | null>(null)
const router = useRouter()

const priceFor = (plant: Plant) => productPricing[plant.name] || { amount: 0, source: '#' }
const formatCurrency = (amount: number) => `${new Intl.NumberFormat('vi-VN').format(amount)}₫`
const cartItemCount = computed(() => cart.value.reduce((total, item) => total + item.quantity, 0))
const cartTotal = computed(() => cart.value.reduce((total, item) => total + priceFor(item.plant).amount * item.quantity, 0))
const selectedQuantity = (plantName: string) => quantities.value[plantName] || 1
const changeSelectedQuantity = (plantName: string, change: number) => {
  quantities.value[plantName] = Math.max(1, selectedQuantity(plantName) + change)
}
const flyToCart = (event: MouseEvent) => {
  const source = (event.currentTarget as HTMLElement).closest('.plant-card')?.querySelector('.plant-image') as HTMLElement | null
  const target = cartButton.value
  if (!source || !target) return
  const from = source.getBoundingClientRect()
  const to = target.getBoundingClientRect()
  const flyer = source.cloneNode() as HTMLElement
  flyer.className = 'flying-plant'
  flyer.style.cssText = `left:${from.left}px;top:${from.top}px;width:${from.width}px;height:${from.height}px;--fly-x:${to.left + to.width / 2 - from.left - from.width / 2}px;--fly-y:${to.top + to.height / 2 - from.top - from.height / 2}px`
  document.body.appendChild(flyer)
  window.setTimeout(() => flyer.remove(), 720)
}
const addToCart = (plant: Plant, quantity = 1) => {
  const item = cart.value.find(entry => entry.plant.name === plant.name)
  if (item) item.quantity += quantity
  else cart.value.push({ plant, quantity })
}
const addSelectedToCart = (plant: Plant, event: MouseEvent) => {
  const quantity = selectedQuantity(plant.name)
  addToCart(plant, quantity)
  flyToCart(event)
  quantities.value[plant.name] = 1
}
const updateQuantity = (plantName: string, quantity: number) => {
  if (quantity < 1) return removeFromCart(plantName)
  const item = cart.value.find(entry => entry.plant.name === plantName)
  if (item) item.quantity = quantity
}
const removeFromCart = (plantName: string) => { cart.value = cart.value.filter(item => item.plant.name !== plantName) }
const handlePaymentProof = (event: Event) => {
  const file = (event.target as HTMLInputElement).files?.[0]
  if (!file) return
  paymentProofName.value = file.name
  paymentProofUrl.value = URL.createObjectURL(file)
  paymentConfirmed.value = false
}
const copyAccount = async () => {
  try { await navigator.clipboard.writeText('0979745413'); copied.value = true; setTimeout(() => { copied.value = false }, 1800) } catch { copied.value = false }
}
const confirmPayment = () => { if (paymentProofName.value) paymentConfirmed.value = true }
onMounted(() => {
  const data = localStorage.getItem('currentUser')
  const savedCart = localStorage.getItem('green_nursery_cart')
  if (data) user.value = JSON.parse(data)
  if (savedCart) cart.value = JSON.parse(savedCart)
})
watch(cart, value => localStorage.setItem('green_nursery_cart', JSON.stringify(value)), { deep: true })
const logout = () => { localStorage.removeItem('currentUser'); router.push('/login') }
const openPlant = (plant: Plant) => { selectedPlant.value = plant }
const closePlant = () => { selectedPlant.value = null }
const growthFor = (plant: Plant) => plant.category === 'Cây lấy gỗ'
  ? 'Cây non bén rễ 3–6 tháng, tạo thân trong 2–3 năm và phát triển tán/gỗ ổn định về sau.'
  : 'Cây non bén rễ 1–3 tháng, phát triển tán trong năm đầu và cho hoa, quả khi đủ tuổi, dinh dưỡng.'
const usageFor = (plant: Plant) => plant.category === 'Cây lấy gỗ'
  ? 'Trồng lấy gỗ, phủ xanh đất trống và tạo bóng mát.'
  : plant.category === 'Cây công nghiệp'
    ? 'Cung cấp nông sản/nguyên liệu và phù hợp phát triển vùng trồng.'
    : 'Cho quả tươi, tạo mảng xanh và làm cây ăn trái trong vườn nhà.'
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;1,600;1,700&display=swap');
.home-page { min-height: 100vh; overflow: hidden; color: #183b29; background: #f7f8f2; font-family: 'DM Sans', sans-serif; }
.site-header { position: absolute; z-index: 2; top: 0; left: 50%; display: flex; align-items: center; justify-content: space-between; width: min(1180px, 100%); padding: 25px 48px; transform: translateX(-50%); color: #fff; }
.brand { display: inline-flex; align-items: center; gap: 10px; color: inherit; text-decoration: none; font-size: 1.08rem; font-weight: 700; }
.brand-mark { display: grid; width: 42px; height: 42px; place-items: center; color: #d8f5ba; border: 1px solid rgba(219,249,187,.45); border-radius: 50%; background: rgba(218,249,188,.12); backdrop-filter: blur(6px); }
.brand-mark svg { width: 30px; height: 30px; }.header-actions { display: flex; align-items: center; gap: 14px; font-size: .87rem; }.welcome { color: rgba(255,255,255,.82); }.welcome strong { color: #d9f4be; }.logout, .cart-button { border: 1px solid rgba(255,255,255,.45); border-radius: 50px; padding: 9px 16px; color: #fff; background: transparent; font: inherit; cursor: pointer; transition: background .2s; }.logout:hover, .cart-button:hover { background: rgba(255,255,255,.15); }.cart-button { display: inline-flex; align-items: center; gap: 7px; }.cart-button span { display: grid; min-width: 18px; height: 18px; place-items: center; color: #1c5a37; border-radius: 50%; background: #d7f2b8; font-size: .67rem; font-weight: 700; }
.hero { position: relative; display: flex; align-items: center; min-height: 570px; padding: 135px max(48px, calc((100% - 1084px) / 2)) 65px; color: #fff; background: linear-gradient(90deg, rgba(8,42,26,.90), rgba(14,69,42,.64) 52%, rgba(4,28,16,.48)), url('https://images.unsplash.com/photo-1441974231531-c6227db76b6e?auto=format&fit=crop&w=2200&q=85') center / cover; }.hero-content { max-width: 680px; }.eyebrow { margin-bottom: 10px; color: #86c66d; text-transform: uppercase; letter-spacing: .14em; font-size: .7rem; font-weight: 700; }h1 { margin: 0 0 17px; font: 700 clamp(2.8rem, 5.3vw, 4.8rem)/1.05 'Playfair Display', Georgia, serif; letter-spacing: -.05em; }h1 em { color: #d1f3ab; }.hero-content > p:not(.eyebrow) { max-width: 490px; color: rgba(250,255,248,.84); font-size: 1.04rem; line-height: 1.75; }.explore { display: inline-flex; gap: 14px; margin-top: 28px; padding: 12px 0; color: #e3f9ca; border-bottom: 1px solid rgba(222,248,187,.55); text-decoration: none; font-weight: 700; }.explore span { font-size: 1.2rem; }.hero-note { position: absolute; right: max(48px, calc((100% - 1084px) / 2)); bottom: 54px; display: flex; align-items: center; gap: 14px; padding-left: 14px; border-left: 1px solid rgba(218,243,191,.6); }.hero-note span { color: #c8efa3; font: 600 1.55rem 'Playfair Display', serif; }.hero-note p { color: rgba(255,255,255,.78); font-size: .78rem; line-height: 1.5; }
.catalog { width: min(1180px, 100%); margin: auto; padding: 78px 48px 88px; }.section-heading { display: flex; align-items: flex-end; justify-content: space-between; gap: 20px; }.section-heading .eyebrow { color: #619558; }.section-heading h2 { margin: 0; font: 700 clamp(2rem, 4vw, 3rem)/1.15 'Playfair Display', Georgia, serif; letter-spacing: -.035em; }.catalog-cart { display: flex; align-items: center; gap: 10px; min-width: 222px; padding: 10px 13px; color: #315e41; border: 1px solid #d2e4ce; border-radius: 12px; background: #fff; box-shadow: 0 7px 18px rgba(35, 80, 46, .07); text-align: left; cursor: pointer; transition: transform .2s, box-shadow .2s; }.catalog-cart:hover { box-shadow: 0 10px 25px rgba(35,80,46,.14); transform: translateY(-2px); }.catalog-cart-icon { display: grid; width: 35px; height: 35px; place-items: center; border-radius: 9px; background: #e2f2d9; font-size: 1rem; }.catalog-cart small, .catalog-cart strong { display: block; }.catalog-cart small { color: #78917d; font-size: .66rem; }.catalog-cart strong { margin-top: 2px; font-size: .76rem; }.catalog-cart b { margin-left: auto; color: #568c5b; font-size: 1.1rem; }.filter-bar { display: flex; gap: 9px; flex-wrap: wrap; margin: 34px 0 28px; }.filter-bar button { padding: 9px 16px; color: #587160; border: 1px solid #d7e1d4; border-radius: 40px; background: transparent; font: 600 .82rem inherit; cursor: pointer; transition: .2s; }.filter-bar button:hover, .filter-bar button.active { color: #fff; border-color: #2e7045; background: #2e7045; }.product-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }.plant-card { overflow: hidden; border: 1px solid #e0e8dd; border-radius: 15px; background: #fff; box-shadow: 0 5px 20px rgba(35,70,43,.045); transition: transform .25s, box-shadow .25s; }.plant-card:hover { box-shadow: 0 13px 26px rgba(35,70,43,.13); transform: translateY(-5px); }.plant-image { position: relative; height: 210px; background-position: center; background-size: cover; }.plant-image::after { position: absolute; inset: 0; content: ''; background: linear-gradient(180deg, rgba(11,42,22,.30), transparent 45%); }.plant-type, .plant-number { position: absolute; z-index: 1; top: 13px; color: #fff; font-size: .68rem; font-weight: 700; }.plant-type { left: 13px; padding: 5px 9px; border-radius: 20px; background: rgba(18,62,36,.68); backdrop-filter: blur(5px); }.plant-number { right: 14px; color: rgba(255,255,255,.9); font-family: 'Playfair Display', serif; font-size: 1rem; }.plant-info { padding: 18px; }.plant-info > p { margin: 0 0 2px; color: #83a27d; font-size: .68rem; font-style: italic; }.plant-info h3 { margin: 0 0 14px; color: #1d442e; font: 700 1.3rem 'Playfair Display', Georgia, serif; }.product-panels { display: grid; gap: 10px; }.detail-panel, .price-panel { border: 1px solid #e1ebde; border-radius: 10px; }.detail-panel { background: #fbfdf9; }.detail-panel summary { display: flex; justify-content: space-between; padding: 10px 11px; color: #396447; font-size: .76rem; font-weight: 700; cursor: pointer; list-style: none; }.detail-panel summary::-webkit-details-marker { display: none; }.detail-panel summary span { color: #5b965c; font-size: 1rem; }.detail-panel[open] summary { border-bottom: 1px solid #e5eee2; }.detail-panel[open] summary span { transform: rotate(45deg); }.detail-panel p { margin: 10px 11px 7px; color: #718275; font-size: .68rem; line-height: 1.5; }.detail-panel ul { display: grid; gap: 6px; margin: 0; padding: 0 11px 9px; color: #657b6b; font-size: .66rem; line-height: 1.45; list-style: none; }.detail-panel li b { color: #3e6c49; }.more-detail { margin: 0 11px 11px; padding: 0; color: #397b4b; border: 0; background: none; font: 700 .68rem inherit; cursor: pointer; }.price-panel { padding: 11px; background: #f2f8ef; }.price-panel small, .price-panel strong { display: block; }.price-panel small { color: #718674; font-size: .65rem; }.price-panel strong { margin: 2px 0 9px; color: #24683e; font: 700 1.2rem 'Playfair Display', serif; }.card-quantity { display: inline-flex; align-items: center; overflow: hidden; margin-bottom: 9px; border: 1px solid #cce0c8; border-radius: 7px; background: #fff; }.card-quantity button { width: 28px; height: 27px; color: #42714c; border: 0; background: #fff; font-size: 1.05rem; cursor: pointer; }.card-quantity span { width: 29px; color: #315b3c; text-align: center; font-size: .75rem; font-weight: 700; }.add-cart { display: flex; width: 100%; justify-content: space-between; padding: 9px 10px; color: #fff; border: 0; border-radius: 7px; background: #2e7045; font: 700 .72rem 'DM Sans', sans-serif; cursor: pointer; }.add-cart:hover { background: #215b35; }.add-cart span { font-size: 1rem; line-height: .8; }footer { padding: 23px; color: #cbe0c7; background: #1c4a30; text-align: center; font-size: .76rem; }.footer-mark { margin-right: 5px; color: #bfe98f; }
.plant-modal { position: fixed; z-index: 20; inset: 0; display: grid; place-items: center; padding: 24px; background: rgba(6, 31, 19, .70); backdrop-filter: blur(7px); }.modal-card { position: relative; display: grid; grid-template-columns: minmax(230px, .85fr) minmax(0, 1.3fr); width: min(930px, 100%); max-height: min(720px, calc(100vh - 48px)); overflow: auto; border: 1px solid rgba(255,255,255,.65); border-radius: 22px; background: #fbfcf8; box-shadow: 0 28px 80px rgba(0, 19, 10, .42); }.modal-image { min-height: 100%; background-position: center; background-size: cover; }.modal-image::after { display: block; height: 100%; min-height: 560px; content: ''; background: linear-gradient(180deg, rgba(10,53,30,.12), rgba(10,53,30,.55)); }.modal-image span { position: absolute; z-index: 1; margin: 22px; padding: 6px 11px; color: #fff; border: 1px solid rgba(255,255,255,.35); border-radius: 20px; background: rgba(16,67,39,.58); font-size: .72rem; font-weight: 700; backdrop-filter: blur(6px); }.close-modal { position: absolute; z-index: 3; top: 14px; right: 14px; display: grid; width: 34px; height: 34px; place-items: center; color: #245438; border: 0; border-radius: 50%; background: rgba(255,255,255,.93); box-shadow: 0 2px 9px rgba(0,0,0,.13); font-size: 1.55rem; line-height: 1; cursor: pointer; }.modal-content { padding: 42px 42px 32px; }.modal-latin { margin: 0 0 4px; color: #71996d; font-size: .73rem; font-style: italic; }.modal-content h2 { margin: 0 0 15px; color: #1b482f; font: 700 2.15rem/1.15 'Playfair Display', Georgia, serif; letter-spacing: -.035em; }.description { margin: 0; color: #5e7465; line-height: 1.7; }.guide-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-top: 27px; }.guide-grid section { padding: 17px; border: 1px solid #e2ebdf; border-radius: 12px; background: #f4f8f1; }.guide-icon { color: #75a167; font: 700 .7rem 'DM Sans', sans-serif; letter-spacing: .1em; }.guide-grid h3 { margin: 4px 0 8px; color: #2d6140; font: 700 1.05rem 'Playfair Display', Georgia, serif; }.guide-grid ul { display: grid; gap: 7px; margin: 0; padding-left: 15px; color: #617869; font-size: .78rem; line-height: 1.47; }.image-source { display: inline-block; margin-top: 23px; color: #337145; border-bottom: 1px solid #9abf90; text-decoration: none; font-size: .76rem; font-weight: 700; }.image-source:hover { color: #1e5b33; }
.price-row { display: flex; align-items: center; justify-content: space-between; gap: 14px; margin-top: 22px; padding: 14px 15px; border: 1px solid #dcebd6; border-radius: 12px; background: #f2f9ee; }.price-row span, .price-row strong, .price-row small { display: block; }.price-row span { color: #718674; font-size: .68rem; }.price-row strong { color: #24683e; font: 700 1.35rem 'Playfair Display', serif; }.price-row small { display: inline; margin-left: 3px; color: #6d8a71; font: 500 .67rem 'DM Sans', sans-serif; }.price-row button, .checkout button { border: 0; border-radius: 8px; color: #fff; background: #2e7045; font: 700 .75rem 'DM Sans', sans-serif; cursor: pointer; }.price-row button { padding: 11px 13px; white-space: nowrap; }.price-row button span { display: inline; color: inherit; font-size: 1.05rem; }.source-links { display: flex; flex-wrap: wrap; gap: 0 18px; }.source-links .image-source { margin-top: 18px; }
.cart-overlay, .checkout-overlay { position: fixed; z-index: 30; inset: 0; background: rgba(6, 32, 19, .58); backdrop-filter: blur(4px); }.cart-drawer { position: absolute; top: 0; right: 0; display: flex; width: min(440px, 100%); height: 100%; flex-direction: column; padding: 28px; color: #244735; background: #fbfdf8; box-shadow: -22px 0 60px rgba(0,20,10,.25); }.cart-header { display: flex; justify-content: space-between; gap: 15px; padding-bottom: 19px; border-bottom: 1px solid #e6eee3; }.cart-header h2, .checkout-card h2 { margin: 0; color: #214d33; font: 700 1.9rem 'Playfair Display', Georgia, serif; letter-spacing: -.035em; }.cart-header button, .close-checkout { display: grid; width: 34px; height: 34px; place-items: center; color: #306045; border: 0; border-radius: 50%; background: #edf4e9; font-size: 1.4rem; cursor: pointer; }.cart-items { display: grid; flex: 1; align-content: start; gap: 4px; padding: 15px 0; overflow: auto; }.cart-item { display: flex; align-items: flex-start; gap: 11px; padding: 10px 0; border-bottom: 1px solid #edf2eb; }.cart-thumb { display: block; width: 57px; height: 57px; flex: none; border-radius: 9px; background-position: center; background-size: cover; }.cart-item-info { flex: 1; }.cart-item-info strong, .cart-item-info small { display: block; }.cart-item-info strong { color: #345440; font-size: .82rem; }.cart-item-info small { margin: 2px 0 7px; color: #7b937f; font-size: .69rem; }.quantity { display: inline-flex; align-items: center; overflow: hidden; border: 1px solid #dce7d8; border-radius: 6px; }.quantity button { width: 24px; height: 22px; color: #497052; border: 0; background: #f4f8f1; font-size: .95rem; cursor: pointer; }.quantity span { display: inline-block; width: 24px; color: #426348; text-align: center; font-size: .72rem; font-weight: 700; }.remove-item { margin-top: 1px; color: #97a99a; border: 0; background: transparent; font-size: 1.15rem; cursor: pointer; }.empty-cart { display: grid; flex: 1; place-content: center; text-align: center; }.empty-cart span { font-size: 2rem; }.empty-cart h3 { margin: 10px 0 3px; color: #31563d; font-size: 1.2rem; }.empty-cart p { margin: 0; color: #849489; font-size: .8rem; }.checkout { padding-top: 16px; border-top: 1px solid #e3ece0; }.subtotal { display: flex; justify-content: space-between; color: #5f7566; font-size: .8rem; }.subtotal strong { color: #245c38; font: 700 1.25rem 'Playfair Display', serif; }.checkout p { margin: 6px 0 13px; color: #8a998e; font-size: .67rem; }.checkout > button { display: flex; width: 100%; justify-content: space-between; padding: 13px 15px; font-size: .83rem; }.checkout > button span { font-size: 1.1rem; }.checkout-overlay { z-index: 40; display: grid; place-items: center; padding: 20px; }.checkout-card { position: relative; width: min(480px, 100%); max-height: calc(100vh - 40px); overflow: auto; padding: 34px; border: 1px solid rgba(255,255,255,.7); border-radius: 18px; color: #365642; background: #fbfdf8; box-shadow: 0 25px 70px rgba(0,20,10,.33); }.close-checkout { position: absolute; top: 15px; right: 15px; }.checkout-note { margin: 8px 0 19px; color: #718378; font-size: .8rem; line-height: 1.5; }.bank-box { display: flex; align-items: center; gap: 12px; padding: 15px; border: 1px solid #d6ead2; border-radius: 11px; background: #f2f9ed; }.bank-icon { display: grid; width: 41px; height: 41px; flex: none; place-items: center; color: #fff; border-radius: 9px; background: #1261a3; font-size: .75rem; font-weight: 700; }.bank-box div { flex: 1; }.bank-box small, .bank-box strong, .bank-box b { display: block; }.bank-box small { color: #77907c; font-size: .64rem; }.bank-box strong { margin-top: 1px; color: #345d41; font-size: .73rem; }.bank-box b { color: #176039; font-size: 1.02rem; letter-spacing: .04em; }.bank-box button { padding: 6px 8px; color: #337046; border: 1px solid #c8e1c4; border-radius: 6px; background: #fff; font: 700 .64rem 'DM Sans', sans-serif; cursor: pointer; }.transfer-total { display: grid; grid-template-columns: 1fr auto; gap: 3px; margin: 17px 0; padding-bottom: 15px; border-bottom: 1px dashed #d6e1d4; }.transfer-total span { color: #6f8274; font-size: .73rem; }.transfer-total strong { color: #235a38; font: 700 1.35rem 'Playfair Display', serif; }.transfer-total small { grid-column: 1 / -1; color: #879889; font-size: .69rem; }.upload-label { display: grid; min-height: 115px; place-items: center; align-content: center; border: 1.5px dashed #a6cca0; border-radius: 11px; background: #f7fbf4; text-align: center; cursor: pointer; }.upload-label input { position: absolute; width: 1px; height: 1px; opacity: 0; }.upload-icon { display: grid; width: 27px; height: 27px; place-items: center; color: #4a8752; border-radius: 50%; background: #ddf1d8; font-size: 1.1rem; }.upload-label strong { margin-top: 5px; color: #3e6948; font-size: .77rem; }.upload-label small { color: #849589; font-size: .65rem; }.payment-preview { display: block; width: 100%; max-height: 155px; margin-top: 10px; border-radius: 8px; object-fit: cover; }.confirm-payment { width: 100%; margin-top: 15px; padding: 13px; font-size: .82rem !important; }.confirm-payment:disabled { cursor: not-allowed; opacity: .45; }.payment-success { margin: 10px 0 0; color: #4d8a58; text-align: center; font-size: .71rem; line-height: 1.45; }
.flying-plant { position: fixed; z-index: 100; overflow: hidden; border-radius: 12px; background-position: center; background-size: cover; pointer-events: none; animation: fly-to-cart .7s cubic-bezier(.2,.75,.25,1) forwards; box-shadow: 0 10px 25px rgba(15, 61, 30, .3); }.flying-plant::after, .flying-plant > * { display: none !important; }@keyframes fly-to-cart { 0% { opacity: 1; transform: scale(1); } 70% { opacity: .86; } 100% { opacity: 0; transform: translate(var(--fly-x), var(--fly-y)) scale(.12) rotate(12deg); } }
@media (max-width: 960px) { .product-grid { grid-template-columns: repeat(3, 1fr); }.site-header, .catalog { padding-left: 28px; padding-right: 28px; }.hero { padding-right: 28px; padding-left: 28px; }.hero-note { right: 28px; } }
@media (max-width: 700px) { .site-header { padding: 18px 20px; }.welcome { display: none; }.header-actions { gap: 0; }.hero { min-height: 520px; padding: 125px 20px 72px; }.hero-note { display: none; }.catalog { padding: 56px 20px 65px; }.section-heading { align-items: flex-start; flex-direction: column; }.catalog-cart { width: 100%; }.product-grid { grid-template-columns: repeat(2, 1fr); gap: 13px; }.plant-image { height: 165px; }.plant-info { padding: 14px; }.plant-info h3 { font-size: 1.08rem; }.section-heading h2 { font-size: 2rem; }.plant-modal { padding: 12px; }.modal-card { display: block; max-height: calc(100vh - 24px); }.modal-image { min-height: 205px; }.modal-image::after { min-height: 205px; }.modal-content { padding: 27px 22px 24px; }.modal-content h2 { font-size: 1.85rem; }.guide-grid { grid-template-columns: 1fr; gap: 11px; margin-top: 20px; } }
@media (max-width: 380px) { .product-grid { grid-template-columns: 1fr; }.plant-image { height: 190px; }.brand { font-size: .95rem; }.brand-mark { width: 36px; height: 36px; } }
</style>
