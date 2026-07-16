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

## جدول الشركات المتاحة

| الشركة | الحزمة | المنطقة |
|---|---|---|
| Bosta | `shipbridge-bosta` | مصر |
| Aramex | `shipbridge-aramex` | الشرق الأوسط / عالمي |
| Mylerz | `shipbridge-mylerz` | مصر |
| Turbo | `shipbridge-turbo` | مصر |
| J&T Express | `shipbridge-jtexpress` | مصر / آسيا |
| SMSA | `shipbridge-smsa` | السعودية / الخليج |
| FedEx | `shipbridge-fedex` | عالمي |
| UPS | `shipbridge-ups` | عالمي |
| DHL Express | `shipbridge-dhl` | عالمي |
| Egypt Post | `shipbridge-egyptpost` | مصر |

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


## أنواع المفاتيح (مهم)

مش كل شركة بنفس طريقة الدخول:

| الشركة | نوع المفاتيح في `.env` |
|---|---|
| Bosta / Mylerz / Turbo / J&T / Egypt Post | `*_API_KEY` |
| SMSA | `SMSA_PASSKEY` |
| Aramex | `USERNAME` + `PASSWORD` + رقم الحساب + PIN |
| FedEx / UPS | `CLIENT_ID` + `CLIENT_SECRET` (+ token اختياري) |
| DHL | `USERNAME` + `PASSWORD` |

التفاصيل الدقيقة لكل شركة موجودة في README الخاص بها وفي ملف `config/` داخل الحزمة.
