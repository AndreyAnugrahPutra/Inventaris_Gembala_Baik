export const adminMenu = [
    { label: "Dashboard", icon: "pi pi-home", route: "admin.dashboard" },
    { label: "Permohonan", icon: "pi pi-file", route: "admin.permohonan.page" },
    { label: "Kategori", icon: "pi pi-tags", route: "admin.kategori.page" },
    { label: "Barang", icon: "pi pi-box", route: "admin.barang.page" },
    { label: "Users", icon: "pi pi-users", route: "admin.users.page" },
    { label: "Unit", icon: "pi pi-id-card", route: "admin.unit.page" },
]
export const adminPanel = [
    {
        label: "Laporan",
        items: [
            {
                label: "Barang",
                icon: "pi pi-box",
                route: "admin.dashboard",
            },
            {
                label: "Permohonan",
                icon: "pi pi-file",
                route: "admin.dashboard",
            },
            {
                label: "Barang Keluar",
                icon: "pi pi-box",
                route: "admin.dashboard",
            },
        ],
    },
];

export const bendaharaMenu = [
    { label: "Dashboard", icon: "pi pi-home", route: "bendahara.dashboard" },
    // { label: "Permohonan", icon: "pi pi-file", route: "bendahara.permohonan.page" },
    { label: "Validasi", icon: "pi pi-file-check", route: "bendahara.permohonan.page" },
];
export const bendaharaPanel = [
    {
        label: "Laporan",
        items: [
            {
                label: "Barang",
                icon: "pi pi-box",
                route: "bendahara.dashboard",
            },
            {
                label: "Permohonan",
                icon: "pi pi-file",
                route: "bendahara.dashboard",
            },
            {
                label: "Barang Keluar",
                icon: "pi pi-box",
                route: "bendahara.dashboard",
            },
        ],
    },
];


