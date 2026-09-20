<style>
/* ─── ACADEMY SIDEBAR – FULLY ISOLATED ─── */
.acad-brand {
    padding: 24px 20px !important;
    margin: 0 !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
    display: flex !important;
    align-items: center !important;
    gap: 12px !important;
    color: #ffffff !important;
    font-weight: 700 !important;
    font-size: 1.2rem !important;
    text-decoration: none !important;
}

.acad-menu {
    list-style: none !important;
    padding: 16px 14px 40px 14px !important;
    margin: 0 !important;
    display: flex !important;
    flex-direction: column !important;
    gap: 4px !important;
    flex: 1 !important;
}

.acad-item {
    list-style: none !important;
    margin: 0 !important;
    padding: 0 !important;
}

.acad-link {
    display: flex !important;
    align-items: center !important;
    gap: 12px !important;
    padding: 12px 16px !important;
    border-radius: 10px !important;
    color: #94a3b8 !important;
    text-decoration: none !important;
    font-size: 0.95rem !important;
    font-weight: 500 !important;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
    border: 1px solid transparent !important;
    background: transparent !important;
    box-shadow: none !important;
    transform: none !important;
}

.acad-link ion-icon {
    font-size: 1.35rem !important;
    min-width: 24px !important;
    color: #64748b !important;
    transition: all 0.2s ease !important;
}

.acad-link:hover {
    color: #ffffff !important;
    background: rgba(255, 166, 0, 0.08) !important;
    border-color: rgba(255, 166, 0, 0.2) !important;
    transform: translateX(4px) !important;
    text-decoration: none !important;
}

.acad-link:hover ion-icon {
    color: #FFA600 !important;
    transform: scale(1.15) !important;
}

.acad-link.acad-active {
    color: #ffffff !important;
    background: linear-gradient(90deg, rgba(255, 166, 0, 0.25) 0%, rgba(255, 166, 0, 0.08) 100%) !important;
    border: 1px solid rgba(255, 166, 0, 0.4) !important;
    border-left: 4px solid #FFA600 !important;
    font-weight: 700 !important;
    box-shadow: 0 4px 15px rgba(255, 166, 0, 0.15) !important;
}

.acad-link.acad-active ion-icon {
    color: #FFA600 !important;
    transform: scale(1.1) !important;
}

.acad-section-label {
    font-size: 0.7rem !important;
    font-weight: 800 !important;
    text-transform: uppercase !important;
    color: #64748b !important;
    padding: 16px 16px 6px 16px !important;
    margin: 0 !important;
    letter-spacing: 1.2px !important;
    list-style: none !important;
}
</style>
