@extends('main.layouts.app')



@section('title', 'MGS MORE')



@push('styles')

<style>

/* ==========================================

   PHOTO GALLERY - Premium Dark Theme

   ========================================== */



.gallery-container {

    padding: 20px;

    background: white;

    border-radius: 10px;

    box-shadow: 0 4px 12px rgba(0,0,0,0.1);

}



/* ====== HEADER ====== */

.gallery-header {

    display: flex;

    align-items: center;

    justify-content: space-between;

    margin-bottom: 20px;

    flex-wrap: wrap;

    gap: 14px;

}

.gallery-title {

    font-size: 1.5rem;

    font-weight: 800;

    background: linear-gradient(135deg, #250190, #045f87);

    -webkit-background-clip: text;

    -webkit-text-fill-color: transparent;

    display: flex;

    align-items: center;

    gap: 10px;

}

.gallery-title i {

    -webkit-text-fill-color: 14121a;

    font-size: 1.2rem;

}



/* ====== TOOLBAR ====== */

.gallery-toolbar {

    display: flex;

    align-items: center;

    justify-content: space-between;

    background:black;

    border: 1px solid rgba(139, 92, 246, 0.1);

    border-radius: 14px;

    padding: 10px 18px;

    margin-bottom: 20px;

    flex-wrap: wrap;

    gap: 12px;

}

.toolbar-left {

    display: flex;

    align-items: center;

    gap: 10px;

    flex-wrap: wrap;

}

.toolbar-right {

    display: flex;

    align-items: center;

    gap: 10px;

}

.gallery-stats {

    font-size: 20px;

    color: #64748b;

    display: flex;

    gap: 20px;

    align-items: center;

}

.gallery-stats span {

    display: flex;

    align-items: center;

    gap: 8px;

}

.gallery-stats b { color: #a78bfa; }



/* View Mode Buttons */

.view-modes {

    display: flex;

    background: rgba(0, 0, 0, 0.25);

    border-radius: 10px;

    padding: 3px;

    gap: 5px;

}

.view-mode-btn {

    width: 35px;

    height: 35px;

    border: none;

    border-radius: 8px;

    background: transparent;

    color: #64748b;

    cursor: pointer;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 22px;

    transition: all 0.2s;

}

.view-mode-btn.active {

    background: rgba(139, 92, 246, 0.3);

    color: white;

}

.view-mode-btn:hover:not(.active) {

    color: #94a3b8;

}



/* Action Buttons */

.gallery-action-btn {

    height: 32px;

    padding: 0 14px;

    border: 1px solid rgba(139, 92, 246, 0.2);

    border-radius: 10px;

    background: ffd157;

    color: black;

    cursor: pointer;

    font-size: 0.78rem;

    font-weight: 600;

    display: flex;

    align-items: center;

    gap: 6px;

    transition: all 0.2s;

    white-space: nowrap;

}

.gallery-action-btn:hover {

    background: rgba(139, 92, 246, 0.25);

    border-color: rgba(139, 92, 246, 0.4);

}

.gallery-action-btn.upload-btn {

    background: rgb(19 138 63);

    border-color: rgba(34, 197, 94, 0.2);

    color: white;

}

.gallery-action-btn.upload-btn:hover {

    background: rgba(34, 197, 94, 0.25);

}



/* ====== BREADCRUMB ====== */

.gallery-breadcrumb {

    display: flex;

    align-items: center;

    gap: 6px;

    margin-bottom: 18px;

    flex-wrap: wrap;

}

.gallery-breadcrumb a {

    color: #aa0000;

    text-decoration: none;

    font-size: 16px;

    transition: color 0.2s;

    cursor: pointer;

}

.gallery-breadcrumb a:hover { color: #a78bfa; }

.gallery-breadcrumb .bc-sep { color: #475569; font-size: 0.68rem; }

.gallery-breadcrumb .bc-current { color: #aa0000; font-weight: 600; font-size: 16px; }



/* ====== LOADING / EMPTY ====== */

.gallery-loading {

    display: flex;

    flex-direction: column;

    align-items: center;

    justify-content: center;

    padding: 80px 20px;

    color: #64748b;

    gap: 12px;

}

.gallery-loading i {

    font-size: 2rem;

    color: #a78bfa;

    animation: spin 1s linear infinite;

}

@keyframes spin { to { transform: rotate(360deg); } }

.gallery-empty {

    text-align: center;

    padding: 80px 20px;

    color: #475569;

}

.gallery-empty i { font-size: 22px; margin-bottom: 16px; color: #334155; }



/* ====== ALBUM CARDS ====== */

.album-section-title {

    font-size: 0.85rem;

    font-weight: 700;

    color: #64748b;

    text-transform: uppercase;

    letter-spacing: 1px;

    margin-bottom: 12px;

    display: flex;

    align-items: center;

    gap: 8px;

}

.album-section-title i { color: #a78bfa; }

.album-grid {

    display: grid;

    grid-template-columns: repeat(auto-fill, minmax(190px, 1fr));

    gap: 14px;

    margin-bottom: 28px;

}

.album-card {

    background: linear-gradient(135deg, rgba(30, 27, 75, 0.6), rgba(20, 20, 50, 0.8));

    border: 1px solid rgba(139, 92, 246, 0.1);

    border-radius: 14px;

    overflow: hidden;

    cursor: pointer;

    transition: all 0.3s ease;

    position: relative;

}

.album-card:hover {

    transform: translateY(-3px);

    border-color: rgba(139, 92, 246, 0.3);

    box-shadow: 0 10px 28px rgba(139, 92, 246, 0.12);

}

.album-thumb {

    width: 100%;

    height: 130px;

    background: rgba(15, 15, 35, 0.8);

    display: flex;

    align-items: center;

    justify-content: center;

    overflow: hidden;

    position: relative;

}

.album-thumb img {

    width: 100%;

    height: 100%;

    object-fit: cover;

    transition: transform 0.4s ease;

}

.album-card:hover .album-thumb img { transform: scale(1.06); }

.album-thumb-placeholder { color: #334155; font-size: 2.2rem; }

.album-count-badge {

    position: absolute;

    bottom: 6px;

    right: 6px;

    background: rgba(0, 0, 0, 0.65);

    color: #e2e8f0;

    font-size: 0.65rem;

    font-weight: 700;

    padding: 2px 8px;

    border-radius: 6px;

    backdrop-filter: blur(6px);

}

.album-info { padding: 10px 12px; }

.album-name {

    color: #e2e8f0;

    font-weight: 600;

    font-size: 0.85rem;

    white-space: nowrap;

    overflow: hidden;

    text-overflow: ellipsis;

}



/* ====== SECTION TITLE FOR IMAGES ====== */

.image-section-title {

    font-size: 0.85rem;

    font-weight: 700;

    color: #64748b;

    text-transform: uppercase;

    letter-spacing: 1px;

    margin-bottom: 12px;

    display: flex;

    align-items: center;

    gap: 8px;

}

.image-section-title i { color: #38bdf8; }



/* ====== THUMBNAIL VIEW (Grid/Masonry) ====== */

.thumb-grid {

    display: grid;

    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));

    gap: 14px;

}

.thumb-item {

    border-radius: 12px;

    overflow: hidden;

    position: relative;

    cursor: pointer;

    transition: all 0.3s ease;

    background: rgba(15, 15, 35, 0.5);

    border: 1px solid rgba(139, 92, 246, 0.06);

    aspect-ratio: 1;

}

.thumb-item:hover {

    border-color: rgba(139, 92, 246, 0.3);

    box-shadow: 0 6px 20px rgba(139, 92, 246, 0.1);

    transform: scale(1.03);

    z-index: 2;

}

.thumb-item img {

    width: 100%;

    height: 100%;

    object-fit: cover;

    display: block;

    transition: opacity 0.3s;

}

.thumb-item img.loading { opacity: 0; }

.thumb-item img.loaded { opacity: 1; }

.thumb-overlay {

    position: absolute;

    bottom: 0;

    left: 0;

    right: 0;

    padding: 8px 10px;

    background: linear-gradient(transparent, rgba(0, 0, 0, 0.75));

    opacity: 0;

    transition: opacity 0.3s;

}

.thumb-item:hover .thumb-overlay { opacity: 1; }

.thumb-overlay .img-name {

    color: #fff;

    font-size: 0.72rem;

    font-weight: 600;

    white-space: nowrap;

    overflow: hidden;

    text-overflow: ellipsis;

}

.thumb-overlay .img-size {

    color: rgba(255, 255, 255, 0.55);

    font-size: 0.62rem;

}

/* Item action buttons */

.item-actions {

    position: absolute;

    top: 6px;

    right: 6px;

    display: flex;

    gap: 4px;

    opacity: 0;

    transition: opacity 0.2s;

    z-index: 3;

}

.thumb-item:hover .item-actions,

.list-row:hover .item-actions { opacity: 1; }

.item-act-btn {

    width: 28px;

    height: 28px;

    border-radius: 8px;

    border: none;

    cursor: pointer;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 0.7rem;

    transition: all 0.2s;

    backdrop-filter: blur(6px);

}

.item-act-btn.rename-btn {

    background: rgba(251, 191, 36, 0.5);

    color: #fef3c7;

}

.item-act-btn.rename-btn:hover { background: rgba(251, 191, 36, 0.7); }

.item-act-btn.delete-btn {

    background: rgba(239, 68, 68, 0.5);

    color: #fecaca;

}

.item-act-btn.delete-btn:hover { background: rgba(239, 68, 68, 0.7); }



/* ====== LIST VIEW ====== */

.list-view {

    border: 1px solid rgba(139, 92, 246, 0.08);

    border-radius: 14px;

    overflow: hidden;

}

.list-header {

    display: grid;

    grid-template-columns: 48px 1fr 100px 140px 80px;

    padding: 10px 16px;

    background: #023449;

    font-size: 0.72rem;

    font-weight: 700;

    color: white;

    text-transform: uppercase;

    letter-spacing: 0.5px;

    align-items: center;

}

.list-row {

    display: grid;

    grid-template-columns: 48px 1fr 100px 140px 80px;

    padding: 8px 16px;

    align-items: center;

    border-top: 1px solid rgba(139, 92, 246, 0.05);

    transition: background 0.2s;

    cursor: pointer;

    position: relative;

}

.list-row:hover {

    background: rgba(139, 92, 246, 0.06);

}

.list-thumb {

    width: 40px;

    height: 40px;

    border-radius: 8px;

    overflow: hidden;

    background: rgba(0, 0, 0, 0.2);

}

.list-thumb img {

    width: 100%;

    height: 100%;

    object-fit: cover;

}

.list-name {

    color: green;

    font-size: 14px;

    font-weight: 600;

    white-space: nowrap;

    overflow: hidden;

    text-overflow: ellipsis;

    padding-right: 12px;

}

.list-size {

    color: black;

    font-size: 0.78rem;

}

.list-date {

    color: red;

    font-weight: 600;

    font-size: 0.76rem;

}

.list-actions-cell {

    display: flex;

    gap: 4px;

    justify-content: flex-end;

}



/* ====== SLIDESHOW VIEW ====== */

.slide-view {

    position: relative;

    width: 100%;

    max-width: 1000px;

    margin: 0 auto;

    border-radius: 16px;

    overflow: hidden;

    background: rgba(0, 0, 0, 0.4);

    border: 1px solid rgba(139, 92, 246, 0.12);

}

.slide-main {

    width: 100%;

    aspect-ratio: 16/10;

    display: flex;

    align-items: center;

    justify-content: center;

    overflow: hidden;

    position: relative;

    background: rgba(10, 10, 30, 0.9);

}

.slide-main img {

    max-width: 100%;

    max-height: 100%;

    object-fit: contain;

    transition: opacity 0.4s;

}

.slide-nav {

    position: absolute;

    top: 50%;

    transform: translateY(-50%);

    background: rgba(255, 255, 255, 0.1);

    border: none;

    color: white;

    width: 46px;

    height: 46px;

    border-radius: 50%;

    font-size: 1rem;

    cursor: pointer;

    display: flex;

    align-items: center;

    justify-content: center;

    transition: all 0.2s;

    z-index: 5;

    backdrop-filter: blur(6px);

}

.slide-nav:hover { background: rgba(255, 255, 255, 0.2); }

.slide-prev { left: 14px; }

.slide-next { right: 14px; }

.slide-info {

    padding: 14px 20px;

    display: flex;

    align-items: center;

    justify-content: space-between;

    background: rgba(20, 20, 50, 0.8);

    border-top: 1px solid rgba(139, 92, 246, 0.08);

    flex-wrap: wrap;

    gap: 10px;

}

.slide-info-left {

    display: flex;

    flex-direction: column;

    gap: 2px;

}

.slide-info-name {

    color: #e2e8f0;

    font-weight: 600;

    font-size: 0.9rem;

}

.slide-info-meta {

    color: #64748b;

    font-size: 0.75rem;

}

.slide-info-right {

    display: flex;

    align-items: center;

    gap: 8px;

}

.slide-counter {

    color: #94a3b8;

    font-size: 0.78rem;

    font-weight: 600;

    margin-right: 6px;

}

.slide-control-btn {

    width: 34px;

    height: 34px;

    border-radius: 10px;

    border: 1px solid rgba(139, 92, 246, 0.15);

    background: rgba(139, 92, 246, 0.08);

    color: #a78bfa;

    cursor: pointer;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 0.82rem;

    transition: all 0.2s;

}

.slide-control-btn:hover {

    background: rgba(139, 92, 246, 0.2);

}

.slide-control-btn.active {

    background: rgba(139, 92, 246, 0.3);

    color: #c4b5fd;

}

.slide-thumbnails {

    display: flex;

    gap: 8px;

    padding: 12px 16px;

    overflow-x: auto;

    background: rgba(10, 10, 30, 0.6);

    border-top: 1px solid rgba(139, 92, 246, 0.05);

}

.slide-thumbnails::-webkit-scrollbar { height: 4px; }

.slide-thumbnails::-webkit-scrollbar-thumb { background: rgba(139, 92, 246, 0.3); border-radius: 4px; }

.slide-thumb {

    width: 56px;

    height: 56px;

    border-radius: 8px;

    overflow: hidden;

    cursor: pointer;

    border: 2px solid transparent;

    flex-shrink: 0;

    transition: all 0.2s;

    opacity: 0.5;

}

.slide-thumb:hover { opacity: 0.8; }

.slide-thumb.active {

    border-color: #a78bfa;

    opacity: 1;

}

.slide-thumb img {

    width: 100%;

    height: 100%;

    object-fit: cover;

}



/* ====== LIGHTBOX ====== */

.lightbox-overlay {

    position: fixed;

    inset: 0;

    background: rgba(0, 0, 0, 0.92);

    z-index: 10000;

    display: none;

    align-items: center;

    justify-content: center;

    backdrop-filter: blur(12px);

}

.lightbox-overlay.show { display: flex; }

.lightbox-close {

    position: absolute;

    top: 18px;

    right: 20px;

    background: rgba(255, 255, 255, 0.1);

    border: none;

    color: white;

    width: 42px;

    height: 42px;

    border-radius: 50%;

    font-size: 1.1rem;

    cursor: pointer;

    display: flex;

    align-items: center;

    justify-content: center;

    transition: all 0.2s;

    z-index: 10;

}

.lightbox-close:hover { background: rgba(255, 255, 255, 0.2); }

.lightbox-img-container {

    max-width: 90vw;

    max-height: 85vh;

    display: flex;

    align-items: center;

    justify-content: center;

}

.lightbox-img-container img {

    max-width: 100%;

    max-height: 85vh;

    object-fit: contain;

    border-radius: 6px;

    box-shadow: 0 16px 48px rgba(0, 0, 0, 0.5);

}

.lightbox-nav {

    position: absolute;

    top: 50%;

    transform: translateY(-50%);

    background: rgba(255, 255, 255, 0.1);

    border: none;

    color: white;

    width: 48px;

    height: 48px;

    border-radius: 50%;

    font-size: 1rem;

    cursor: pointer;

    display: flex;

    align-items: center;

    justify-content: center;

    transition: all 0.2s;

    z-index: 10;

}

.lightbox-nav:hover { background: rgba(255, 255, 255, 0.2); }

.lightbox-prev { left: 18px; }

.lightbox-next { right: 18px; }

.lightbox-info {

    position: absolute;

    bottom: 18px;

    left: 50%;

    transform: translateX(-50%);

    background: rgba(0, 0, 0, 0.6);

    backdrop-filter: blur(8px);

    padding: 8px 18px;

    border-radius: 10px;

    color: #e2e8f0;

    text-align: center;

    max-width: 80%;

    z-index: 10;

}

.lightbox-info .lb-name { font-weight: 600; font-size: 0.85rem; }

.lightbox-info .lb-counter { color: #94a3b8; font-size: 0.72rem; margin-top: 2px; }

.lightbox-download {

    position: absolute;

    top: 18px;

    right: 74px;

    background: rgba(255, 255, 255, 0.1);

    border: none;

    color: white;

    width: 42px;

    height: 42px;

    border-radius: 50%;

    font-size: 0.95rem;

    cursor: pointer;

    display: flex;

    align-items: center;

    justify-content: center;

    transition: all 0.2s;

    z-index: 10;

    text-decoration: none;

}

.lightbox-download:hover { background: rgba(255, 255, 255, 0.2); color: white; }



/* ====== UPLOAD MODAL ====== */

.upload-modal {

    background: linear-gradient(135deg, rgba(30, 27, 75, 0.98), rgba(20, 20, 50, 1));

    border: 1px solid rgba(139, 92, 246, 0.2);

    border-radius: 18px;

    padding: 28px 32px;

    width: 92%;

    max-width: 520px;

    animation: modalIn 0.3s ease;

}

@keyframes modalIn {

    from { opacity: 0; transform: scale(0.95) translateY(-10px); }

    to { opacity: 1; transform: scale(1) translateY(0); }

}

.upload-dropzone {

    border: 2px dashed rgba(139, 92, 246, 0.25);

    border-radius: 14px;

    padding: 40px 20px;

    text-align: center;

    color: #64748b;

    transition: all 0.3s;

    cursor: pointer;

    margin: 16px 0;

}

.upload-dropzone:hover, .upload-dropzone.drag-over {

    border-color: #a78bfa;

    background: rgba(139, 92, 246, 0.05);

    color: #a78bfa;

}

.upload-dropzone i { font-size: 2.2rem; margin-bottom: 10px; }

.upload-dropzone p { font-size: 0.85rem; margin-top: 8px; }

.upload-progress {

    margin-top: 12px;

    display: none;

}

.upload-progress-bar-bg {

    height: 6px;

    background: rgba(255, 255, 255, 0.06);

    border-radius: 4px;

    overflow: hidden;

}

.upload-progress-bar {

    height: 100%;

    background: linear-gradient(90deg, #a78bfa, #38bdf8);

    border-radius: 4px;

    width: 0%;

    transition: width 0.3s;

}

.upload-file-list {

    max-height: 140px;

    overflow-y: auto;

    margin-top: 10px;

}

.upload-file-item {

    display: flex;

    align-items: center;

    justify-content: space-between;

    padding: 6px 10px;

    font-size: 0.78rem;

    color: #94a3b8;

    background: rgba(255, 255, 255, 0.03);

    border-radius: 8px;

    margin-bottom: 4px;

}

.upload-file-item .ufi-name {

    white-space: nowrap;

    overflow: hidden;

    text-overflow: ellipsis;

    max-width: 280px;

}

.upload-file-item .ufi-size { color: #475569; font-size: 0.72rem; }

.upload-file-item .ufi-remove {

    background: none;

    border: none;

    color: #ef4444;

    cursor: pointer;

    font-size: 0.72rem;

    padding: 2px 4px;

}



/* ====== RENAME MODAL ====== */

.rename-input {

    width: 100%;

    background: rgba(255, 255, 255, 0.05);

    border: 1px solid rgba(139, 92, 246, 0.2);

    border-radius: 10px;

    padding: 10px 14px;

    color: #e2e8f0;

    font-size: 0.9rem;

    outline: none;

    margin: 14px 0;

    box-sizing: border-box;

}

.rename-input:focus { border-color: #a78bfa; }



/* ====== MODAL (shared) ====== */

.g-modal-overlay {

    position: fixed;

    inset: 0;

    background: rgba(0, 0, 0, 0.6);

    z-index: 9000;

    display: none;

    align-items: center;

    justify-content: center;

    backdrop-filter: blur(6px);

}

.g-modal-overlay.show { display: flex; }

.g-modal {

    background: linear-gradient(135deg, rgba(30, 27, 75, 0.98), rgba(20, 20, 50, 1));

    border: 1px solid rgba(139, 92, 246, 0.2);

    border-radius: 18px;

    padding: 24px 28px;

    width: 92%;

    max-width: 460px;

    animation: modalIn 0.3s ease;

}

.g-modal h3 {

    color: #e2e8f0;

    font-size: 1.05rem;

    margin-bottom: 4px;

    display: flex;

    align-items: center;

    gap: 8px;

}

.g-modal-actions {

    display: flex;

    justify-content: flex-end;

    gap: 10px;

    margin-top: 14px;

}

.g-btn-cancel {

    padding: 8px 18px;

    border: 1px solid rgba(139, 92, 246, 0.15);

    border-radius: 10px;

    background: transparent;

    color: #94a3b8;

    cursor: pointer;

    font-size: 0.82rem;

    transition: all 0.2s;

}

.g-btn-cancel:hover { background: rgba(255, 255, 255, 0.05); }

.g-btn-confirm {

    padding: 8px 18px;

    border: none;

    border-radius: 10px;

    background: linear-gradient(135deg, #7c3aed, #6d28d9);

    color: white;

    cursor: pointer;

    font-size: 0.82rem;

    font-weight: 600;

    transition: all 0.2s;

}

.g-btn-confirm:hover { filter: brightness(1.15); }

.g-btn-confirm.danger {

    background: linear-gradient(135deg, #dc2626, #b91c1c);

}



/* ====== RESPONSIVE ====== */

@media (max-width: 1100px) {

    .thumb-grid { grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); }

}

@media (max-width: 768px) {

    .gallery-container { padding: 14px; }

    .gallery-toolbar { padding: 8px 12px; }

    .thumb-grid { grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 10px; }

    .album-grid { grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 10px; }

    .list-header, .list-row {

        grid-template-columns: 40px 1fr 80px;

    }

    .list-header > *:nth-child(4),

    .list-header > *:nth-child(5),

    .list-row > *:nth-child(4),

    .list-row > *:nth-child(5) { display: none; }

    .lightbox-nav { width: 38px; height: 38px; }

}

@media (max-width: 480px) {

    .thumb-grid { grid-template-columns: 1fr 1fr; }

    .album-grid { grid-template-columns: 1fr 1fr; }

}



/* ====== BACK BUTTON ====== */

.gallery-back-btn {

    display: inline-flex;

    align-items: center;

    gap: 6px;

    padding: 6px 14px;

    border: 1px solid rgba(139, 92, 246, 0.2);

    border-radius: 10px;

    background: rgba(139, 92, 246, 0.08);

    color: #a78bfa;

    cursor: pointer;

    font-size: 0.8rem;

    font-weight: 600;

    transition: all 0.2s;

    margin-bottom: 14px;

}

.gallery-back-btn:hover { background: rgba(139, 92, 246, 0.2); }



/* ====== ALBUM HOVER ACTIONS ====== */

.album-actions {

    position: absolute;

    top: 6px;

    right: 6px;

    display: flex;

    gap: 3px;

    opacity: 0;

    transition: opacity 0.2s;

    z-index: 3;

}

.album-card:hover .album-actions { opacity: 1; }

.album-act-btn {

    width: 26px;

    height: 26px;

    border-radius: 7px;

    border: none;

    cursor: pointer;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 0.65rem;

    transition: all 0.2s;

    backdrop-filter: blur(6px);

}

.album-act-btn.edit { background: rgba(251, 191, 36, 0.6); color: #fef3c7; }

.album-act-btn.edit:hover { background: rgba(251, 191, 36, 0.8); }

.album-act-btn.del { background: rgba(239, 68, 68, 0.6); color: #fecaca; }

.album-act-btn.del:hover { background: rgba(239, 68, 68, 0.8); }

.album-act-btn.move { background: rgba(56, 189, 248, 0.6); color: #e0f2fe; }

.album-act-btn.move:hover { background: rgba(56, 189, 248, 0.8); }



/* ====== MOVE BUTTON ON IMAGES ====== */

.item-act-btn.move-btn { background: rgba(56, 189, 248, 0.5); color: #e0f2fe; }

.item-act-btn.move-btn:hover { background: rgba(56, 189, 248, 0.7); }



/* ====== UPLOADER BADGE ====== */

.uploader-badge {

    color: rgba(255,255,255,0.5);

    font-size: 0.6rem;

    display: flex;

    align-items: center;

    gap: 3px;

}

.uploader-badge i { font-size: 0.55rem; }

.list-uploader { color: #64748b; font-size: 0.72rem; }



/* ====== MOVE MODAL SELECT ====== */

.move-select {

    width: 100%;

    background: rgba(255,255,255,0.05);

    border: 1px solid rgba(139,92,246,0.2);

    border-radius: 10px;

    padding: 10px 14px;

    color: #e2e8f0;

    font-size: 0.85rem;

    outline: none;

    margin: 14px 0;

    box-sizing: border-box;

}

.move-select:focus { border-color: #a78bfa; }

.move-select option { background: #1e1b4b; color: #e2e8f0; }



/* ====== FOLDER TREE PICKER ====== */

.folder-tree-container {

    max-height: 260px;

    overflow-y: auto;

    background: rgba(255,255,255,0.03);

    border: 1px solid rgba(139,92,246,0.15);

    border-radius: 10px;

    margin: 12px 0;

    padding: 6px 0;

}

.folder-tree-container::-webkit-scrollbar { width: 5px; }

.folder-tree-container::-webkit-scrollbar-thumb { background: rgba(139,92,246,0.3); border-radius: 10px; }

.ft-item {

    display: flex;

    align-items: center;

    gap: 8px;

    padding: 7px 12px;

    cursor: pointer;

    color: #94a3b8;

    font-size: 0.8rem;

    transition: all 0.15s;

    border-left: 3px solid transparent;

}

.ft-item:hover { background: rgba(139,92,246,0.08); color: #e2e8f0; }

.ft-item.selected {

    background: rgba(139,92,246,0.15);

    color: #a78bfa;

    border-left-color: #a78bfa;

    font-weight: 600;

}

.ft-item i { width: 16px; text-align: center; font-size: 0.75rem; }

.ft-indent { display: inline-block; }



/* ====== MULTI-SELECT LIST ====== */

.list-checkbox {

    width: 18px; height: 18px;

    accent-color: #a78bfa;

    cursor: pointer;

    flex-shrink: 0;

}

.list-row.selected { background: rgba(139, 92, 246, 0.12) !important; }

.batch-bar {

    position: fixed;

    bottom: 24px;

    left: 50%;

    transform: translateX(-50%);

    display: flex;

    align-items: center;

    gap: 12px;

    padding: 10px 20px;

    background: rgba(30, 27, 75, 0.95);

    border: 1px solid rgba(139, 92, 246, 0.3);

    border-radius: 14px;

    backdrop-filter: blur(12px);

    box-shadow: 0 8px 32px rgba(0,0,0,0.4);

    z-index: 100;

    animation: batchBarIn 0.25s ease;

}

@keyframes batchBarIn { from { transform: translateX(-50%) translateY(30px); opacity: 0; } }

.batch-bar-count {

    color: #a78bfa;

    font-weight: 700;

    font-size: 0.85rem;

    min-width: 80px;

}

.batch-bar-btn {

    display: flex;

    align-items: center;

    gap: 6px;

    padding: 7px 14px;

    border-radius: 8px;

    border: none;

    cursor: pointer;

    font-size: 0.78rem;

    font-weight: 600;

    transition: all 0.2s;

}

.batch-bar-btn.move { background: rgba(56,189,248,0.2); color: #38bdf8; }

.batch-bar-btn.move:hover { background: rgba(56,189,248,0.35); }

.batch-bar-btn.del { background: rgba(239,68,68,0.2); color: #ef4444; }

.batch-bar-btn.del:hover { background: rgba(239,68,68,0.35); }

.batch-bar-btn.cancel { background: rgba(148,163,184,0.15); color: #94a3b8; }

.batch-bar-btn.cancel:hover { background: rgba(148,163,184,0.25); }

/* ====== VIDEO CARD ====== */
.video-thumb-item {
    border-radius: 12px; overflow: hidden; position: relative; cursor: pointer;
    transition: all 0.3s ease; background: rgba(15,15,35,0.5);
    border: 1px solid rgba(139,92,246,0.06); aspect-ratio: 16/10;
}
.video-thumb-item:hover { border-color: rgba(139,92,246,0.3); box-shadow: 0 6px 20px rgba(139,92,246,0.1); transform: scale(1.03); z-index: 2; }
.video-thumb-item video.vid-thumb { width: 100%; height: 100%; object-fit: cover; display: block; }
.video-thumb-item .play-overlay { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.25); transition: background 0.3s; }
.video-thumb-item:hover .play-overlay { background: rgba(0,0,0,0.4); }
.video-thumb-item .play-icon { font-size: 2.5rem; color: rgba(255,255,255,0.85); text-shadow: 0 2px 12px rgba(0,0,0,0.5); transition: transform 0.3s; }
.video-thumb-item:hover .play-icon { transform: scale(1.15); }
.video-thumb-item .video-ext-badge { position: absolute; top: 8px; left: 8px; background: rgba(139,92,246,0.8); color: white; font-size: 0.6rem; font-weight: 700; padding: 2px 8px; border-radius: 5px; text-transform: uppercase; }
.video-thumb-item .thumb-overlay { position: absolute; bottom: 0; left: 0; right: 0; padding: 8px 10px; background: linear-gradient(transparent, rgba(0,0,0,0.75)); opacity: 0; transition: opacity 0.3s; }
.video-thumb-item:hover .thumb-overlay { opacity: 1; }
.video-thumb-item:hover .item-actions { opacity: 1; }

/* ====== VIDEO PLAYER MODAL ====== */
.vp-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.92); z-index: 10000; display: none; align-items: center; justify-content: center; flex-direction: column; backdrop-filter: blur(12px); }
.vp-overlay.show { display: flex; }
.vp-close { position: absolute; top: 18px; right: 20px; background: rgba(255,255,255,0.1); border: none; color: white; width: 42px; height: 42px; border-radius: 50%; font-size: 1.1rem; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s; z-index: 10; }
.vp-close:hover { background: rgba(255,255,255,0.2); }
.vp-video-container { width: 90vw; max-width: 1000px; }
.vp-video-container video { width: 100%; max-height: 80vh; border-radius: 10px; background: black; }
.vp-title { text-align: center; color: #e2e8f0; font-size: 0.9rem; margin-top: 12px; font-weight: 600; }
.vp-download { position: absolute; top: 18px; right: 74px; background: rgba(255,255,255,0.1); border: none; color: white; width: 42px; height: 42px; border-radius: 50%; font-size: 0.95rem; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s; z-index: 10; text-decoration: none; }
.vp-download:hover { background: rgba(255,255,255,0.2); color: white; }

/* ====== DOWNLOAD BUTTON ====== */
.item-act-btn.download-btn { background: rgba(34,197,94,0.5); color: #bbf7d0; text-decoration: none; }
.item-act-btn.download-btn:hover { background: rgba(34,197,94,0.7); }
</style>

@endpush



@section('content')

<div class="gallery-container">

    <!-- Header -->

    <div class="gallery-header">

        <div class="gallery-title">

            <i class="fas fa-camera-retro"></i> MGS MORE

        </div>

    </div>



    <!-- Breadcrumb -->

    <div class="gallery-breadcrumb" id="galleryBreadcrumb"></div>



    <!-- Toolbar -->

    <div class="gallery-toolbar">

        <div class="toolbar-left">

            <div class="view-modes">

                <button class="view-mode-btn active" data-mode="thumbnail" title="Dạng lưới" onclick="setViewMode('thumbnail')"><i class="fas fa-th"></i></button>

                <button class="view-mode-btn" data-mode="list" title="Dạng danh sách" onclick="setViewMode('list')"><i class="fas fa-list"></i></button>

                <button class="view-mode-btn" data-mode="slide" title="Dạng trình chiếu" onclick="setViewMode('slide')"><i class="fas fa-play-circle"></i></button>

            </div>

            <div class="gallery-stats">

                <span><i class="fas fa-folder"></i> <b id="statFolders">0</b> album</span>

                <span><i class="fas fa-image"></i> <b id="statImages">0</b> ảnh</span>
                <span><i class="fas fa-video"></i> <b id="statVideos">0</b> video</span>

            </div>

        </div>

        <div class="toolbar-right">

            <button class="gallery-action-btn" id="btnCreateAlbum" style="display:none;" onclick="openCreateAlbumModal()">

                <i class="fas fa-folder-plus"></i> Tạo Album

            </button>

            <button class="gallery-action-btn upload-btn" id="btnUpload" style="display:none;" onclick="openUploadModal()">

                <i class="fas fa-cloud-upload-alt"></i> Upload

            </button>

        </div>

    </div>



    <!-- Content -->

    <div id="galleryContent">

        <div class="gallery-loading">

            <i class="fas fa-spinner"></i>

            <span>Đang tải thư viện ảnh...</span>

        </div>

    </div>

</div>



<!-- Lightbox -->

<div class="lightbox-overlay" id="lightbox">

    <button class="lightbox-close" onclick="closeLightbox()"><i class="fas fa-times"></i></button>

    <a class="lightbox-download" id="lbDownload" href="#" target="_blank" title="Tải xuống"><i class="fas fa-download"></i></a>

    <button class="lightbox-nav lightbox-prev" onclick="lightboxNav(-1)"><i class="fas fa-chevron-left"></i></button>

    <button class="lightbox-nav lightbox-next" onclick="lightboxNav(1)"><i class="fas fa-chevron-right"></i></button>

    <div class="lightbox-img-container">

        <img id="lbImage" src="" alt="">

    </div>

    <div class="lightbox-info">

        <div class="lb-name" id="lbName"></div>

        <div class="lb-counter" id="lbCounter"></div>

    </div>

</div>

<!-- Video Player Modal -->
<div class="vp-overlay" id="videoPlayerModal">
    <button class="vp-close" onclick="closeVideoPlayer()"><i class="fas fa-times"></i></button>
    <a class="vp-download" id="vpDownload" href="#" target="_blank" title="Tải xuống"><i class="fas fa-download"></i></a>
    <div class="vp-video-container">
        <video id="vpVideo" controls autoplay playsinline></video>
    </div>
    <div class="vp-title" id="vpTitle"></div>
</div>


<!-- Upload Modal -->

<div class="g-modal-overlay" id="uploadModal">

    <div class="upload-modal">

        <h3><i class="fas fa-cloud-upload-alt" style="color:#22c55e;"></i> Upload Ảnh / Video</h3>

        <div class="upload-dropzone" id="uploadDropzone" onclick="document.getElementById('fileInput').click()">

            <i class="fas fa-cloud-upload-alt"></i>

            <p>Kéo thả ảnh/video vào đây hoặc click để chọn</p>

        </div>

        <input type="file" id="fileInput" multiple accept="image/*,video/*" style="display:none;" onchange="handleFileSelect(this.files)">

        <div class="upload-file-list" id="uploadFileList"></div>

        <div class="upload-progress" id="uploadProgress">

            <div class="upload-progress-bar-bg">

                <div class="upload-progress-bar" id="uploadProgressBar"></div>

            </div>

        </div>

        <div class="g-modal-actions">

            <button class="g-btn-cancel" onclick="closeUploadModal()">Hủy</button>

            <button class="g-btn-confirm" id="btnStartUpload" onclick="startUpload()" disabled>Tải lên</button>

        </div>

    </div>

</div>



<!-- Rename Modal -->

<div class="g-modal-overlay" id="renameModal">

    <div class="g-modal">

        <h3><i class="fas fa-edit" style="color:#fbbf24;"></i> Đổi Tên</h3>

        <input type="text" class="rename-input" id="renameInput" placeholder="Tên mới...">

        <div class="g-modal-actions">

            <button class="g-btn-cancel" onclick="closeRenameModal()">Hủy</button>

            <button class="g-btn-confirm" onclick="submitRename()">Lưu</button>

        </div>

    </div>

</div>



<!-- Delete Confirm Modal -->

<div class="g-modal-overlay" id="deleteModal">

    <div class="g-modal">

        <h3><i class="fas fa-trash" style="color:#ef4444;"></i> Xác Nhận Xóa</h3>

        <p style="color:#94a3b8; font-size:0.85rem; margin:12px 0;">Bạn có chắc muốn xóa <strong id="deleteItemName" style="color:#e2e8f0;"></strong>?</p>

        <div class="g-modal-actions">

            <button class="g-btn-cancel" onclick="closeDeleteModal()">Hủy</button>

            <button class="g-btn-confirm danger" onclick="submitDelete()">Xóa</button>

        </div>

    </div>

</div>

<!-- Move Modal -->

<div class="g-modal-overlay" id="moveModal">

    <div class="g-modal">

        <h3><i class="fas fa-arrows-alt" style="color:#38bdf8;"></i> Di Chuyển</h3>

        <p style="color:#94a3b8; font-size:0.82rem; margin:8px 0;">Di chuyển <strong id="moveItemName" style="color:#e2e8f0;"></strong> tới:</p>

        <div class="folder-tree-container" id="moveTreeContainer"></div>

        <input type="hidden" id="moveDestValue" value="">

        <div class="g-modal-actions">

            <button class="g-btn-cancel" onclick="closeMoveModal()">Hủy</button>

            <button class="g-btn-confirm" onclick="submitMove()">Di chuyển</button>

        </div>

    </div>

</div>

<!-- Create Album Modal -->

<div class="g-modal-overlay" id="createAlbumModal">

    <div class="g-modal">

        <h3><i class="fas fa-folder-plus" style="color:#a78bfa;"></i> Tạo Album Mới</h3>

        <input type="text" class="rename-input" id="albumNameInput" placeholder="Tên album...">

        <div class="g-modal-actions">

            <button class="g-btn-cancel" onclick="closeCreateAlbumModal()">Hủy</button>

            <button class="g-btn-confirm" onclick="submitCreateAlbum()">Tạo</button>

        </div>

    </div>

</div>

@endsection



@push('scripts')

<script>

const csrfToken = '{{ csrf_token() }}';

const BASE_S3_PATH = 'Trại Giống';

let currentFolder = '';

let galleryImages = [];
let galleryVideos = [];

let currentPerms = { can_upload: false, can_rename: false, can_delete: false };

let viewMode = 'thumbnail';

let lightboxIndex = 0;

let slideIndex = 0;

let slideAutoPlay = false;

let slideTimer = null;



// Rename/Delete targets

let renameTarget = { path: '', type: 'file' };

let deleteTarget = { path: '', type: 'file', name: '' };

let moveTarget = { path: '', type: 'file', name: '' };



// Upload

let uploadFiles = [];

let allFolders = [];



document.addEventListener('DOMContentLoaded', () => loadGallery(''));



// ============================

// LOAD GALLERY

// ============================

async function loadGallery(folder) {

    currentFolder = folder;

    const content = document.getElementById('galleryContent');

    content.innerHTML = '<div class="gallery-loading"><i class="fas fa-spinner"></i><span>Đang tải...</span></div>';



    try {

        const resp = await fetch("{{ route('media.mgsGalleryApi') }}?folder=" + encodeURIComponent(folder));

        const data = await resp.json();



        if (!data.success) {

            content.innerHTML = `<div class="gallery-empty"><i class="fas fa-exclamation-triangle"></i><p>${data.message || 'Lỗi tải dữ liệu'}</p></div>`;

            return;

        }



        // Stats

        document.getElementById('statFolders').textContent = data.folders.length;
        document.getElementById('statImages').textContent = data.images.length;
        document.getElementById('statVideos').textContent = (data.videos || []).length;



        // Permissions

        currentPerms = data.permissions || { can_upload: false, can_rename: false, can_delete: false };

        document.getElementById('btnUpload').style.display = currentPerms.can_upload ? '' : 'none';

        document.getElementById('btnCreateAlbum').style.display = currentPerms.can_upload ? '' : 'none';



        // Store folders for move modal

        allFolders = data.allFolders || [];



        // Breadcrumb

        renderBreadcrumb(data.breadcrumb);



        // Store images & videos
        galleryImages = data.images;
        galleryVideos = data.videos || [];
        slideIndex = 0;



        // Build content

        let html = '';



        // Back button

        if (currentFolder) {

            const parts = currentFolder.split('/');

            parts.pop();

            const parentFolder = parts.join('/');

            html += `<button class="gallery-back-btn" onclick="loadGallery('${escapeHtml(parentFolder)}')"><i class="fas fa-arrow-left"></i> Quay lại</button>`;

        }



        // Albums

        if (data.folders.length > 0) {

            html += '<div class="album-section-title"><i class="fas fa-folder-open"></i> Album</div>';

            html += '<div class="album-grid">';

            data.folders.forEach(f => {

                const thumbHtml = f.thumbnail

                    ? `<img src="${f.thumbnail}" alt="${escapeHtml(f.name)}" loading="lazy">`

                    : '<div class="album-thumb-placeholder"><i class="fas fa-folder"></i></div>';

                // Full S3 path for album actions

                const fullAlbumPath = currentFolder ? 'Trại Giống/' + currentFolder + '/' + f.name : 'Trại Giống/' + f.name;

                let albumActionsHtml = '<div class="album-actions">';

                if (currentPerms.can_rename) {

                    albumActionsHtml += `<button class="album-act-btn edit" onclick="event.stopPropagation(); openRenameModal('${escapeJs(fullAlbumPath)}', '${escapeJs(f.name)}', 'folder')" title="Đổi tên"><i class="fas fa-edit"></i></button>`;

                }

                if (currentPerms.can_upload) {

                    albumActionsHtml += `<button class="album-act-btn move" onclick="event.stopPropagation(); openMoveModal('${escapeJs(fullAlbumPath)}', 'folder', '${escapeJs(f.name)}')" title="Di chuyển"><i class="fas fa-arrows-alt"></i></button>`;

                }

                if (currentPerms.can_delete) {

                    albumActionsHtml += `<button class="album-act-btn del" onclick="event.stopPropagation(); openDeleteModal('${escapeJs(fullAlbumPath)}', 'folder', '${escapeJs(f.name)}')" title="Xóa"><i class="fas fa-trash"></i></button>`;

                }

                albumActionsHtml += '</div>';

                html += `

                <div class="album-card" onclick="loadGallery('${escapeHtml(f.path)}')">

                    ${albumActionsHtml}

                    <div class="album-thumb">

                        ${thumbHtml}

                        <div class="album-count-badge">${f.count} file</div>

                    </div>

                    <div class="album-info">

                        <div class="album-name" title="${escapeHtml(f.name)}">${escapeHtml(f.name)}</div>

                    </div>

                </div>`;

            });

            html += '</div>';

        }



        // Images & Videos
        if (data.images.length > 0 || galleryVideos.length > 0) {

            html += '<div class="image-section-title"><i class="fas fa-photo-video"></i> Ảnh & Video</div>';

            html += renderCurrentView();

        }



        if (data.folders.length === 0 && data.images.length === 0 && galleryVideos.length === 0) {
            let backBtn = '';
            if (currentFolder) {
                const parentParts = currentFolder.split('/');

                parentParts.pop();

                const parentFolder = parentParts.join('/');

                backBtn = `<button class="gallery-action-btn" onclick="loadGallery('${escapeJs(parentFolder)}')" style="margin:12px auto 0;"><i class="fas fa-arrow-left" style="padding-top:15px"></i> Quay lại</button>`;

            }

            html = `<div class="gallery-empty"><i class="fas fa-images"></i><p>Thư mục này chưa có ảnh/video nào.</p>${backBtn}</div>`;

        }



        content.innerHTML = html;



    } catch (err) {

        content.innerHTML = `<div class="gallery-empty"><i class="fas fa-exclamation-triangle"></i><p>Lỗi: ${err.message}</p></div>`;

    }

}



// ============================

// VIEW MODES

// ============================

function setViewMode(mode) {

    viewMode = mode;

    document.querySelectorAll('.view-mode-btn').forEach(b => b.classList.toggle('active', b.dataset.mode === mode));

    refreshView();

}



function refreshView() {
    if (galleryImages.length === 0 && galleryVideos.length === 0) return;

    const titleEl = document.querySelector('.image-section-title');

    const container = document.getElementById('galleryContent');

    // Find existing view container

    let existingView = container.querySelector('.thumb-grid, .list-view, .slide-view');

    if (existingView) {

        const newHtml = renderCurrentView();

        const wrapper = document.createElement('div');

        wrapper.innerHTML = newHtml;

        existingView.replaceWith(wrapper.firstElementChild);

    }

    // Stop slideshow if switching away

    if (viewMode !== 'slide') stopSlideshow();

}



function renderCurrentView() {

    if (viewMode === 'thumbnail') return renderThumbnailView();

    if (viewMode === 'list') return renderListView();

    if (viewMode === 'slide') return renderSlideView();

}



// ---- THUMBNAIL VIEW ----

function renderThumbnailView() {
    let html = '<div class="thumb-grid">';
    galleryImages.forEach((img, idx) => {
        const actions = renderItemActions(img, idx);
        html += `
        <div class="thumb-item" onclick="openLightbox(${idx})">
            ${actions}
            <img src="${img.url}" alt="${escapeHtml(img.name)}" class="loading" onload="this.classList.remove('loading'); this.classList.add('loaded');" loading="lazy">
            <div class="thumb-overlay">
                <div class="img-name">${escapeHtml(img.name)}</div>
                <div class="img-size">${img.size ? formatSize(img.size) : ''}${img.uploaded_by ? ' · <span class="uploader-badge"><i class="fas fa-user"></i> ' + escapeHtml(img.uploaded_by) + '</span>' : ''}</div>
            </div>
        </div>`;
    });
    // Video cards
    galleryVideos.forEach((v, idx) => {
        const actions = renderItemActions(v, idx, true);
        html += `
        <div class="video-thumb-item" onclick="openVideoPlayer(${idx})">
            ${actions}
            <video class="vid-thumb" src="${escapeHtml(v.url)}#t=1" preload="metadata" muted></video>
            <div class="play-overlay">
                <span class="video-ext-badge">${v.extension}</span>
                <i class="fas fa-play-circle play-icon"></i>
            </div>
            <div class="thumb-overlay">
                <div class="img-name">${escapeHtml(v.name)}</div>
                <div class="img-size">${v.size ? formatSize(v.size) : ''}</div>
            </div>
        </div>`;
    });
    html += '</div>';
    return html;
}



// ---- LIST VIEW ----

function renderListView() {
    let html = '<div class="list-view">';
    html += '<div class="list-header"><span><input type="checkbox" class="list-checkbox" id="selectAllCb" onchange="toggleSelectAll(this)"></span><span>Tên</span><span>Kích thước</span><span>Người tải</span><span></span></div>';
    galleryImages.forEach((img, idx) => {
        const dateStr = img.lastModified ? new Date(img.lastModified * 1000).toLocaleDateString('vi-VN') : '';
        let actionsHtml = '';
        if (currentPerms.can_rename) actionsHtml += `<button class="item-act-btn rename-btn" onclick="event.stopPropagation(); openRenameModal('${escapeJs(img.path)}', '${escapeJs(img.name)}', 'file')" title="Đổi tên"><i class="fas fa-edit"></i></button>`;
        if (currentPerms.can_upload) actionsHtml += `<button class="item-act-btn move-btn" onclick="event.stopPropagation(); openMoveModal('${escapeJs(img.path)}', 'file', '${escapeJs(img.name)}')" title="Di chuyển"><i class="fas fa-arrows-alt"></i></button>`;
        if (currentPerms.can_delete) actionsHtml += `<button class="item-act-btn delete-btn" onclick="event.stopPropagation(); openDeleteModal('${escapeJs(img.path)}', 'file', '${escapeJs(img.name)}')" title="Xóa"><i class="fas fa-trash"></i></button>`;
        actionsHtml += `<a class="item-act-btn download-btn" href="${escapeHtml(img.url)}" download="${escapeHtml(img.name)}" onclick="event.stopPropagation()" title="Tải về"><i class="fas fa-download"></i></a>`;
        html += `
        <div class="list-row" data-idx="${idx}" data-path="${escapeHtml(img.path)}">
            <div><input type="checkbox" class="list-checkbox item-cb" data-idx="${idx}" onclick="event.stopPropagation(); toggleSelect(${idx})"></div>
            <div class="list-name" title="${escapeHtml(img.name)}" onclick="openLightbox(${idx})">${escapeHtml(img.name)}${dateStr ? '<br><span class="list-uploader">' + dateStr + '</span>' : ''}</div>
            <div class="list-size">${img.size ? formatSize(img.size) : '-'}</div>
            <div class="list-date">${img.uploaded_by ? escapeHtml(img.uploaded_by) : '-'}</div>
            <div class="list-actions-cell">${actionsHtml}</div>
        </div>`;
    });
    // Video rows
    galleryVideos.forEach((v, idx) => {
        let actionsHtml = '';
        if (currentPerms.can_rename) actionsHtml += `<button class="item-act-btn rename-btn" onclick="event.stopPropagation(); openRenameModal('${escapeJs(v.path)}', '${escapeJs(v.name)}', 'file')" title="Đổi tên"><i class="fas fa-edit"></i></button>`;
        if (currentPerms.can_upload) actionsHtml += `<button class="item-act-btn move-btn" onclick="event.stopPropagation(); openMoveModal('${escapeJs(v.path)}', 'file', '${escapeJs(v.name)}')" title="Di chuyển"><i class="fas fa-arrows-alt"></i></button>`;
        if (currentPerms.can_delete) actionsHtml += `<button class="item-act-btn delete-btn" onclick="event.stopPropagation(); openDeleteModal('${escapeJs(v.path)}', 'file', '${escapeJs(v.name)}')" title="Xóa"><i class="fas fa-trash"></i></button>`;
        actionsHtml += `<a class="item-act-btn download-btn" href="${escapeHtml(v.url)}" download="${escapeHtml(v.name)}" onclick="event.stopPropagation()" title="Tải về"><i class="fas fa-download"></i></a>`;
        html += `
        <div class="list-row" data-path="${escapeHtml(v.path)}">
            <div></div>
            <div class="list-name" title="${escapeHtml(v.name)}" onclick="openVideoPlayer(${idx})"><i class="fas fa-film" style="color:#a78bfa; margin-right:6px;"></i>${escapeHtml(v.name)}</div>
            <div class="list-size">${v.size ? formatSize(v.size) : '-'}</div>
            <div class="list-date">${v.uploaded_by ? escapeHtml(v.uploaded_by) : '-'}</div>
            <div class="list-actions-cell">${actionsHtml}</div>
        </div>`;
    });
    html += '</div>';
    return html;
}



// ---- SLIDE VIEW ----

function renderSlideView() {

    if (galleryImages.length === 0) return '';

    const img = galleryImages[slideIndex] || galleryImages[0];

    let html = '<div class="slide-view">';

    html += `<div class="slide-main">

        <button class="slide-nav slide-prev" onclick="slideNav(-1)"><i class="fas fa-chevron-left"></i></button>

        <img id="slideMainImg" src="${img.url}" alt="${escapeHtml(img.name)}">

        <button class="slide-nav slide-next" onclick="slideNav(1)"><i class="fas fa-chevron-right"></i></button>

    </div>`;

    html += `<div class="slide-info">

        <div class="slide-info-left">

            <div class="slide-info-name" id="slideInfoName">${escapeHtml(img.name)}</div>

            <div class="slide-info-meta" id="slideInfoMeta">${img.size ? formatSize(img.size) : ''}</div>

        </div>

        <div class="slide-info-right">

            <span class="slide-counter" id="slideCounter">${slideIndex + 1} / ${galleryImages.length}</span>

            <button class="slide-control-btn" id="btnSlidePlay" onclick="toggleSlideshow()" title="Tự động chuyển"><i class="fas fa-play"></i></button>

            <button class="slide-control-btn" onclick="openLightbox(slideIndex)" title="Xem toàn màn hình"><i class="fas fa-expand"></i></button>`;

    if (currentPerms.can_rename) {

        html += `<button class="slide-control-btn" onclick="openRenameModal(galleryImages[slideIndex].path, galleryImages[slideIndex].name, 'file')" title="Đổi tên"><i class="fas fa-edit"></i></button>`;

    }

    if (currentPerms.can_delete) {

        html += `<button class="slide-control-btn" onclick="openDeleteModal(galleryImages[slideIndex].path, 'file', galleryImages[slideIndex].name)" title="Xóa"><i class="fas fa-trash"></i></button>`;

    }

    html += `</div></div>`;



    // Thumbnail strip

    html += '<div class="slide-thumbnails" id="slideThumbs">';

    galleryImages.forEach((i, idx) => {

        html += `<div class="slide-thumb ${idx === slideIndex ? 'active' : ''}" onclick="goToSlide(${idx})"><img src="${i.url}" alt="" loading="lazy"></div>`;

    });

    html += '</div>';

    html += '</div>';

    return html;

}



function slideNav(dir) {

    slideIndex += dir;

    if (slideIndex < 0) slideIndex = galleryImages.length - 1;

    if (slideIndex >= galleryImages.length) slideIndex = 0;

    updateSlide();

}

function goToSlide(idx) {

    slideIndex = idx;

    updateSlide();

}

function updateSlide() {

    const img = galleryImages[slideIndex];

    if (!img) return;

    const mainImg = document.getElementById('slideMainImg');

    if (mainImg) mainImg.src = img.url;

    const nameEl = document.getElementById('slideInfoName');

    if (nameEl) nameEl.textContent = img.name;

    const metaEl = document.getElementById('slideInfoMeta');

    if (metaEl) metaEl.textContent = img.size ? formatSize(img.size) : '';

    const counterEl = document.getElementById('slideCounter');

    if (counterEl) counterEl.textContent = `${slideIndex + 1} / ${galleryImages.length}`;

    // Thumbnails

    document.querySelectorAll('.slide-thumb').forEach((t, i) => t.classList.toggle('active', i === slideIndex));

    // Scroll active thumb into view

    const activeThumb = document.querySelector('.slide-thumb.active');

    if (activeThumb) activeThumb.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });

}

function toggleSlideshow() {

    slideAutoPlay = !slideAutoPlay;

    const btn = document.getElementById('btnSlidePlay');

    if (btn) {

        btn.innerHTML = slideAutoPlay ? '<i class="fas fa-pause"></i>' : '<i class="fas fa-play"></i>';

        btn.classList.toggle('active', slideAutoPlay);

    }

    if (slideAutoPlay) {

        slideTimer = setInterval(() => slideNav(1), 3000);

    } else {

        stopSlideshow();

    }

}

function stopSlideshow() {

    slideAutoPlay = false;

    if (slideTimer) { clearInterval(slideTimer); slideTimer = null; }

    const btn = document.getElementById('btnSlidePlay');

    if (btn) {

        btn.innerHTML = '<i class="fas fa-play"></i>';

        btn.classList.remove('active');

    }

}



// ============================

// ITEM ACTIONS (for thumb view)

// ============================

function renderItemActions(item, idx, isVideo) {
    let html = '<div class="item-actions">';
    if (currentPerms.can_rename) {
        html += `<button class="item-act-btn rename-btn" onclick="event.stopPropagation(); openRenameModal('${escapeJs(item.path)}', '${escapeJs(item.name)}', 'file')" title="Đổi tên"><i class="fas fa-edit"></i></button>`;
    }
    if (currentPerms.can_upload) {
        html += `<button class="item-act-btn move-btn" onclick="event.stopPropagation(); openMoveModal('${escapeJs(item.path)}', 'file', '${escapeJs(item.name)}')" title="Di chuyển"><i class="fas fa-arrows-alt"></i></button>`;
    }
    if (currentPerms.can_delete) {
        html += `<button class="item-act-btn delete-btn" onclick="event.stopPropagation(); openDeleteModal('${escapeJs(item.path)}', 'file', '${escapeJs(item.name)}')" title="Xóa"><i class="fas fa-trash"></i></button>`;
    }
    html += `<a class="item-act-btn download-btn" href="${escapeHtml(item.url)}" download="${escapeHtml(item.name)}" onclick="event.stopPropagation()" title="Tải về"><i class="fas fa-download"></i></a>`;
    html += '</div>';
    return html;
}

// ============================
// VIDEO PLAYER
// ============================
function openVideoPlayer(idx) {
    const v = galleryVideos[idx];
    if (!v) return;
    const modal = document.getElementById('videoPlayerModal');
    const video = document.getElementById('vpVideo');
    video.src = v.url;
    video.play();
    document.getElementById('vpTitle').textContent = v.name;
    document.getElementById('vpDownload').href = v.url;
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
}
function closeVideoPlayer() {
    const modal = document.getElementById('videoPlayerModal');
    const video = document.getElementById('vpVideo');
    video.pause();
    video.src = '';
    modal.classList.remove('show');
    document.body.style.overflow = '';
}



// ============================

// BREADCRUMB

// ============================

function renderBreadcrumb(crumbs) {

    const el = document.getElementById('galleryBreadcrumb');

    let html = '';

    crumbs.forEach((c, i) => {

        if (i > 0) html += '<span class="bc-sep"><i class="fas fa-chevron-right"></i></span>';

        if (i === crumbs.length - 1) {

            html += `<span class="bc-current">${escapeHtml(c.name)}</span>`;

        } else {

            html += `<a onclick="loadGallery('${escapeHtml(c.path)}')">${escapeHtml(c.name)}</a>`;

        }

    });

    el.innerHTML = html;

}



// ============================

// LIGHTBOX

// ============================

function openLightbox(idx) {

    lightboxIndex = idx;

    showLightboxImage();

    document.getElementById('lightbox').classList.add('show');

    document.body.style.overflow = 'hidden';

}

function closeLightbox() {

    document.getElementById('lightbox').classList.remove('show');

    document.body.style.overflow = '';

}

function lightboxNav(dir) {

    lightboxIndex += dir;

    if (lightboxIndex < 0) lightboxIndex = galleryImages.length - 1;

    if (lightboxIndex >= galleryImages.length) lightboxIndex = 0;

    showLightboxImage();

}

function showLightboxImage() {

    const img = galleryImages[lightboxIndex];

    if (!img) return;

    document.getElementById('lbImage').src = img.url;

    document.getElementById('lbName').textContent = img.name;

    document.getElementById('lbCounter').textContent = `${lightboxIndex + 1} / ${galleryImages.length}`;

    document.getElementById('lbDownload').href = img.url;

}



// ============================

// UPLOAD

// ============================

function openUploadModal() {

    uploadFiles = [];

    document.getElementById('fileInput').value = '';

    document.getElementById('uploadFileList').innerHTML = '';

    document.getElementById('uploadProgress').style.display = 'none';

    document.getElementById('uploadProgressBar').style.width = '0%';

    document.getElementById('btnStartUpload').disabled = true;

    document.getElementById('uploadModal').classList.add('show');

}

function closeUploadModal() {

    document.getElementById('uploadModal').classList.remove('show');

}

function handleFileSelect(files) {

    uploadFiles = Array.from(files);

    renderUploadList();

}

function renderUploadList() {

    const el = document.getElementById('uploadFileList');

    el.innerHTML = '';

    uploadFiles.forEach((f, i) => {

        el.innerHTML += `<div class="upload-file-item">

            <span class="ufi-name">${escapeHtml(f.name)}</span>

            <span class="ufi-size">${formatSize(f.size)}</span>

            <button class="ufi-remove" onclick="removeUploadFile(${i})"><i class="fas fa-times"></i></button>

        </div>`;

    });

    document.getElementById('btnStartUpload').disabled = uploadFiles.length === 0;

}

function removeUploadFile(idx) {

    uploadFiles.splice(idx, 1);

    renderUploadList();

}

async function startUpload() {

    if (uploadFiles.length === 0) return;

    const progressEl = document.getElementById('uploadProgress');

    const progressBar = document.getElementById('uploadProgressBar');

    progressEl.style.display = 'block';

    document.getElementById('btnStartUpload').disabled = true;



    const s3Path = currentFolder ? BASE_S3_PATH + '/' + currentFolder : BASE_S3_PATH;



    const formData = new FormData();

    formData.append('path', s3Path);

    for (const file of uploadFiles) {

        formData.append('files[]', file);

    }



    try {

        progressBar.style.width = '50%';

        const resp = await fetch("{{ route('media.upload') }}", {

            method: 'POST',

            headers: { 'X-CSRF-TOKEN': csrfToken },

            body: formData

        });

        const data = await resp.json();

        progressBar.style.width = '100%';



        if (!data.success) {

            alert(data.message || 'Lỗi upload');

        }

    } catch (e) {

        alert('Lỗi upload: ' + e.message);

    }



    closeUploadModal();

    loadGallery(currentFolder);

}



// Drag & drop

const dz = document.getElementById('uploadDropzone');

if (dz) {

    dz.addEventListener('dragover', e => { e.preventDefault(); dz.classList.add('drag-over'); });

    dz.addEventListener('dragleave', () => dz.classList.remove('drag-over'));

    dz.addEventListener('drop', e => {

        e.preventDefault();

        dz.classList.remove('drag-over');

        if (e.dataTransfer.files.length) {

            handleFileSelect(e.dataTransfer.files);

        }

    });

}



// ============================

// MULTI-SELECT / BATCH ACTIONS

// ============================

let selectedItems = new Set();



function toggleSelect(idx) {

    if (selectedItems.has(idx)) {

        selectedItems.delete(idx);

    } else {

        selectedItems.add(idx);

    }

    updateBatchBar();

    // Highlight row

    const row = document.querySelector(`.list-row[data-idx="${idx}"]`);

    if (row) row.classList.toggle('selected', selectedItems.has(idx));

}



function toggleSelectAll(el) {

    const cbs = document.querySelectorAll('.item-cb');

    if (el.checked) {

        galleryImages.forEach((_, i) => selectedItems.add(i));

        cbs.forEach(c => c.checked = true);

        document.querySelectorAll('.list-row').forEach(r => r.classList.add('selected'));

    } else {

        selectedItems.clear();

        cbs.forEach(c => c.checked = false);

        document.querySelectorAll('.list-row').forEach(r => r.classList.remove('selected'));

    }

    updateBatchBar();

}



function updateBatchBar() {

    let bar = document.getElementById('batchBar');

    if (selectedItems.size > 0) {

        if (!bar) {

            bar = document.createElement('div');

            bar.id = 'batchBar';

            bar.className = 'batch-bar';

            document.body.appendChild(bar);

        }

        let btns = `<span class="batch-bar-count">${selectedItems.size} ảnh đã chọn</span>`;

        if (currentPerms.can_upload) {

            btns += `<button class="batch-bar-btn move" onclick="batchMove()"><i class="fas fa-arrows-alt"></i> Di chuyển</button>`;

        }

        if (currentPerms.can_delete) {

            btns += `<button class="batch-bar-btn del" onclick="batchDelete()"><i class="fas fa-trash"></i> Xóa</button>`;

        }

        btns += `<button class="batch-bar-btn cancel" onclick="clearSelection()"><i class="fas fa-times"></i> Bỏ chọn</button>`;

        bar.innerHTML = btns;

    } else if (bar) {

        bar.remove();

    }

}



function clearSelection() {

    selectedItems.clear();

    document.querySelectorAll('.item-cb').forEach(c => c.checked = false);

    document.querySelectorAll('.list-row').forEach(r => r.classList.remove('selected'));

    const sa = document.getElementById('selectAllCb');

    if (sa) sa.checked = false;

    updateBatchBar();

}



async function batchDelete() {

    if (!confirm(`Bạn có chắc muốn xóa ${selectedItems.size} ảnh?`)) return;

    let count = 0;

    for (const idx of selectedItems) {

        const img = galleryImages[idx];

        if (!img) continue;

        try {

            await fetch("{{ route('media.delete') }}", {

                method: 'DELETE',

                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },

                body: JSON.stringify({ path: img.path, type: 'file' })

            });

            count++;

        } catch (e) {}

    }

    clearSelection();

    loadGallery(currentFolder);

}



function batchMove() {

    moveTarget = { path: '__batch__', type: 'batch', name: selectedItems.size + ' ảnh' };

    document.getElementById('moveItemName').textContent = selectedItems.size + ' ảnh đã chọn';

    renderFolderTree();

    document.getElementById('moveModal').classList.add('show');

}



// ============================

// MOVE

// ============================

function renderFolderTree() {

    const container = document.getElementById('moveTreeContainer');

    document.getElementById('moveDestValue').value = '';

    let html = '';

    allFolders.forEach(f => {

        const depth = f.path ? f.path.split('/').length - 1 : 0;

        const indent = depth * 18;

        const icon = f.path === '' ? 'fa-home' : 'fa-folder';

        const label = f.path === '' ? 'Album Kỉ Niệm (gốc)' : f.path.split('/').pop();

        html += `<div class="ft-item" data-path="${escapeHtml(f.path)}" onclick="selectTreeFolder(this)" style="padding-left:${12 + indent}px;">

            <i class="fas ${icon}"></i> ${escapeHtml(label)}

        </div>`;

    });

    container.innerHTML = html;

    // Auto-select root

    const first = container.querySelector('.ft-item');

    if (first) selectTreeFolder(first);

}



function selectTreeFolder(el) {

    document.querySelectorAll('#moveTreeContainer .ft-item').forEach(e => e.classList.remove('selected'));

    el.classList.add('selected');

    document.getElementById('moveDestValue').value = el.dataset.path;

}



function openMoveModal(path, type, name) {

    moveTarget = { path, type, name };

    document.getElementById('moveItemName').textContent = name;

    renderFolderTree();

    document.getElementById('moveModal').classList.add('show');

}

function closeMoveModal() {

    document.getElementById('moveModal').classList.remove('show');

}

async function submitMove() {

    const dest = document.getElementById('moveDestValue').value;

    try {

        if (moveTarget.type === 'batch') {

            for (const idx of selectedItems) {

                const img = galleryImages[idx];

                if (!img) continue;

                await fetch("{{ route('media.move') }}", {

                    method: 'POST',

                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },

                    body: JSON.stringify({ source_path: img.path, dest_folder: dest, type: 'file' })

                });

            }

            clearSelection();

            closeMoveModal();

            loadGallery(currentFolder);

        } else {

            const resp = await fetch("{{ route('media.move') }}", {

                method: 'POST',

                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },

                body: JSON.stringify({ source_path: moveTarget.path, dest_folder: dest, type: moveTarget.type })

            });

            const data = await resp.json();

            if (data.success) {

                closeMoveModal();

                loadGallery(currentFolder);

            } else {

                alert(data.message || 'Lỗi di chuyển');

            }

        }

    } catch (err) {

        alert('Lỗi: ' + err.message);

    }

}



// ============================

// RENAME

// ============================

function openRenameModal(path, name, type) {

    renameTarget = { path, type };

    document.getElementById('renameInput').value = name;

    document.getElementById('renameModal').classList.add('show');

    setTimeout(() => document.getElementById('renameInput').focus(), 100);

}

function closeRenameModal() {

    document.getElementById('renameModal').classList.remove('show');

}

async function submitRename() {

    const newName = document.getElementById('renameInput').value.trim();

    if (!newName) return alert('Vui lòng nhập tên mới.');

    try {

        const resp = await fetch("{{ route('media.rename') }}", {

            method: 'POST',

            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },

            body: JSON.stringify({ old_path: renameTarget.path, new_name: newName, type: renameTarget.type })

        });

        const data = await resp.json();

        if (data.success) {

            closeRenameModal();

            loadGallery(currentFolder);

        } else {

            alert(data.message || 'Lỗi đổi tên');

        }

    } catch (err) {

        alert('Lỗi: ' + err.message);

    }

}



// ============================

// DELETE

// ============================

function openDeleteModal(path, type, name) {

    deleteTarget = { path, type, name };

    document.getElementById('deleteItemName').textContent = name;

    document.getElementById('deleteModal').classList.add('show');

}

function closeDeleteModal() {

    document.getElementById('deleteModal').classList.remove('show');

}

async function submitDelete() {

    try {

        const resp = await fetch("{{ route('media.delete') }}", {

            method: 'DELETE',

            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },

            body: JSON.stringify({ path: deleteTarget.path, type: deleteTarget.type })

        });

        const data = await resp.json();

        if (data.success) {

            closeDeleteModal();

            loadGallery(currentFolder);

        } else {

            alert(data.message || 'Lỗi xóa');

        }

    } catch (err) {

        alert('Lỗi: ' + err.message);

    }

}



// ============================

// CREATE ALBUM

// ============================

function openCreateAlbumModal() {

    document.getElementById('albumNameInput').value = '';

    document.getElementById('createAlbumModal').classList.add('show');

    setTimeout(() => document.getElementById('albumNameInput').focus(), 100);

}

function closeCreateAlbumModal() {

    document.getElementById('createAlbumModal').classList.remove('show');

}

async function submitCreateAlbum() {

    const name = document.getElementById('albumNameInput').value.trim();

    if (!name) return alert('Vui lòng nhập tên album.');

    const s3Path = currentFolder ? BASE_S3_PATH + '/' + currentFolder : BASE_S3_PATH;

    try {

        const resp = await fetch("{{ route('media.createFolder') }}", {

            method: 'POST',

            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },

            body: JSON.stringify({ folder_name: name, path: s3Path })

        });

        const data = await resp.json();

        if (data.success) {

            closeCreateAlbumModal();

            loadGallery(currentFolder);

        } else {

            alert(data.message || 'Lỗi tạo album');

        }

    } catch (err) {

        alert('Lỗi: ' + err.message);

    }

}



// ============================

// KEYBOARD

// ============================

document.addEventListener('keydown', function(e) {

    // Lightbox

    const lb = document.getElementById('lightbox');

    if (lb.classList.contains('show')) {

        if (e.key === 'Escape') closeLightbox();

        if (e.key === 'ArrowLeft') lightboxNav(-1);

        if (e.key === 'ArrowRight') lightboxNav(1);

        return;

    }

    // Rename enter

    if (e.key === 'Enter' && document.getElementById('renameModal').classList.contains('show')) {

        submitRename();

        return;

    }

    // Escape modals

    if (e.key === 'Escape') {
        closeUploadModal();
        closeRenameModal();
        closeDeleteModal();
        closeCreateAlbumModal();
        closeMoveModal();
        closeVideoPlayer();
    }

    // Enter in album name input

    if (e.key === 'Enter' && document.getElementById('createAlbumModal').classList.contains('show')) {

        submitCreateAlbum();

        return;

    }

    // Slide view arrow keys

    if (viewMode === 'slide' && !document.querySelector('.g-modal-overlay.show')) {

        if (e.key === 'ArrowLeft') slideNav(-1);

        if (e.key === 'ArrowRight') slideNav(1);

        if (e.key === ' ') { e.preventDefault(); toggleSlideshow(); }

    }

});



// Click outside lightbox image to close

document.getElementById('lightbox').addEventListener('click', function(e) {

    if (e.target === this || e.target.classList.contains('lightbox-img-container')) closeLightbox();

});



// ============================

// HELPERS

// ============================

function formatSize(bytes) {

    if (!bytes) return '';

    if (bytes < 1024) return bytes + ' B';

    if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';

    return (bytes / 1048576).toFixed(1) + ' MB';

}

function escapeHtml(str) {

    if (!str) return '';

    return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');

}

function escapeJs(str) {

    if (!str) return '';

    return str.replace(/\\/g, '\\\\').replace(/'/g, "\\'").replace(/"/g, '\\"');

}

</script>

@endpush

