# Deploy ke Render (Docker)

## 1. Push berkas deploy ke GitHub

```bash
git add Dockerfile .dockerignore docker/start.sh bootstrap/app.php DEPLOY-RENDER.md
git commit -m "Siapkan deploy Docker untuk Render"
git push origin main
```

## 2. Basis data PostgreSQL

Buat lebih dulu: Render (New > Postgres, Free, region sama dengan web service;
kedaluwarsa 30 hari) atau Aiven PostgreSQL (Free, tanpa batas waktu).

## 3. New > Web Service

| Field | Isi |
| --- | --- |
| Language | **Docker** (bukan Node) |
| Branch | `main` |
| Region | Sama dengan basis data |
| Root Directory | Kosong |
| Instance Type | Free |
| Health Check Path (Advanced) | `/up` |

## 4. Environment Variables

| Key | Value |
| --- | --- |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_KEY` | hasil `php artisan key:generate --show` (jangan pakai tombol Generate) |
| `APP_URL` | `https://<nama-layanan>.onrender.com` (perbarui setelah URL muncul) |
| `LOG_CHANNEL` | `stderr` |
| `DB_CONNECTION` | `pgsql` |
| `DB_URL` | Render: Internal Database URL / Aiven: Service URI |
| `SESSION_DRIVER` | `database` |
| `CACHE_STORE` | `database` |
| `QUEUE_CONNECTION` | `sync` (paket gratis tidak punya worker antrean) |

## 5. Deploy

Saat kontainer mulai, `docker/start.sh` menjalankan `php artisan optimize`,
`php artisan migrate --force`, lalu Apache. Pantau tab **Logs** sampai status **Live**.

Catatan: filesystem Render gratis tidak persisten (unggahan berkas hilang saat
redeploy) dan layanan tidur setelah 15 menit tanpa trafik.
