# دليل الشحن — ShipBridge (بسيط جدًا)

## الفكرة في جملة واحدة
**ShipBridge** = ريموت كنترول واحد لكل شركات الشحن.
كل شركة ليها **حزمة لوحدها** (زي قطعة تتركّب في الريموت).

```
تطبيقك (Laravel)
        │
        ▼
   ShipBridge          ← الحزمة الأساسية (واجهة واحدة)
        │
   ┌────┼────┬────┬────┐
   ▼    ▼    ▼    ▼    ▼
 Bosta Aramex FedEx UPS DHL ...
```

---

## إيه اللي تثبّته؟

| عايز تعمل إيه؟ | ثبّت إيه؟ |
|---|---|
| الأساس (لازم) | `mohamedhekal/shipbridge` |
| شحن بـ Bosta | + `mohamedhekal/shipbridge-bosta` |
| شحن بـ Aramex | + `mohamedhekal/shipbridge-aramex` |
| أكتر من شركة | ثبّت الأساس + كل الشركات اللي محتاجها |

---

## مثال عملي (Bosta)

```bash
composer require mohamedhekal/shipbridge mohamedhekal/shipbridge-bosta
```

في `.env`:
```env
SHIPBRIDGE_DRIVER=bosta
BOSTA_API_KEY=xxxx
```

في الكود:
```php
use Hekal\ShipBridge\Facades\ShipBridge;
use Hekal\ShipBridge\DTOs\Address;
use Hekal\ShipBridge\DTOs\CreateShipmentRequest;
use Hekal\ShipBridge\DTOs\Parcel;

$shipment = ShipBridge::createShipment(new CreateShipmentRequest(
    origin: new Address('المخزن', 'شارع ١', 'القاهرة', 'EG'),
    destination: new Address('العميل', '١٢ النيل', 'الجيزة', 'EG', phone: '01000000000'),
    parcels: [new Parcel(weightKg: 1.0)],
    reference: 'ORD-1',
));

// تتبع
ShipBridge::track($shipment->trackingNumber);

// ليبل
ShipBridge::label($shipment->id);
```

لو عندك أكتر من شركة في نفس المشروع:
```php
ShipBridge::driver('bosta')->createShipment(...);
ShipBridge::driver('aramex')->createShipment(...);
ShipBridge::driver('fedex')->createShipment(...);
```

---

## جدول الشركات المتاحة (كلها `^0.2` — API حقيقي)

| الشركة | الحزمة | المنطقة | نوع الربط |
|---|---|---|---|
| Bosta | `shipbridge-bosta` | مصر | Business API |
| Aramex | `shipbridge-aramex` | الشرق الأوسط / عالمي | SOAP V2 |
| Mylerz | `shipbridge-mylerz` | مصر / المنطقة | Integration API |
| Turbo | `shipbridge-turbo` | مصر | External API |
| J&T Express | `shipbridge-jtexpress` | مصر | Open Platform |
| SMSA | `shipbridge-smsa` | السعودية / الخليج | SECOM SOAP |
| FedEx | `shipbridge-fedex` | عالمي | REST Ship/Track |
| UPS | `shipbridge-ups` | عالمي | REST OAuth2 |
| DHL Express | `shipbridge-dhl` | عالمي | MyDHL |
| Egypt Post | `shipbridge-egyptpost` | مصر | تتبع رسمي + بوابة شريك |
| MNG Kargo | `shipbridge-mng` | تركيا | ApiZone REST |
| HepsiJet | `shipbridge-hepsijet` | تركيا | Integration REST |
| Yurtiçi Kargo | `shipbridge-yurtici` | تركيا | SOAP |
| Aras Kargo | `shipbridge-aras` | تركيا | SOAP |
| Sürat Kargo | `shipbridge-surat` | تركيا | SOAP |
| PTT Kargo | `shipbridge-ptt` | تركيا | SOAP |

```bash
composer require mohamedhekal/shipbridge-bosta:^0.2
# تركيا:
composer require mohamedhekal/shipbridge-mng:^0.2
```

---

## ليه كل شركة في repo لوحدها؟
- تثبّت **بس اللي محتاجه** (أخف وأأمن)
- تحديث Bosta مش بيكسر FedEx
- كل شركة ليها مفاتيح وإعدادات مستقلة

---

## ملاحظات مهمة
1. لازم مفاتيح API من لوحة الشركة نفسها.
2. الحالات (delivered / in_transit ...) بتتوحّد تلقائيًا عن طريق ShipBridge.
3. للتطوير المحلي من غير API: استخدم درايفر `fake` المدمج في ShipBridge.
4. مصر بوست: إنشاء الشحنات يحتاج بوابة شريك (`partner`) — التتبع شغال من TrackTrace الرسمي.

---

## أنواع المفاتيح (مهم)

مش كل شركة بنفس طريقة الدخول:

| الشركة | نوع المفاتيح في `.env` |
|---|---|
| Bosta | `BOSTA_API_KEY` |
| Mylerz | `MYLERZ_USERNAME` + `MYLERZ_PASSWORD` |
| Turbo | `TURBO_AUTHENTICATION_KEY` + `TURBO_MAIN_CLIENT_CODE` |
| J&T | `API_ACCOUNT` + `PRIVATE_KEY` + `CUSTOMER_CODE` + `CUSTOMER_PWD` |
| SMSA | `SMSA_PASSKEY` |
| Aramex | `USERNAME` + `PASSWORD` + رقم الحساب + PIN |
| FedEx / UPS | `CLIENT_ID` + `CLIENT_SECRET` + رقم الحساب |
| DHL | `USERNAME` + `PASSWORD` + رقم الحساب |
| Egypt Post | تتبع بدون مفتاح / أو `EGYPTPOST_API_KEY` لوضع الشريك |

التفاصيل الدقيقة لكل شركة في README الخاص بها و`docs/GUIDE_AR.md` داخل الحزمة.
