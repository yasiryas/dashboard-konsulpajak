export type DashboardStats = {
    totalKlien: number;
    laporanBerjalan: number;
    butuhDokumen: number;
    deadlineTerdekat: number;
};

export type ClientRow = {
    id: number;
    namaEntitas: string;
    jenisKlien: string;
    npwp: string | null;
    email: string;
    paket: string | null;
    laporanTerakhir?: {
        id: number;
        jenisLaporan: string;
        periode: string;
        status: string;
        statusLabel: string;
        deadline: string | null;
    } | null;
};

export type DeadlineItem = {
    id: number;
    jenisLaporan: string;
    periode: string;
    status: string;
    statusLabel: string;
    deadline: string | null;
    client: {
        namaEntitas: string;
        jenisKlien: string;
    };
};

export type PackageOption = {
    id: number;
    nama_paket: string;
    jenis_klien: string;
    jenis_klien_label: string;
};

export type ClientTaxReport = {
    id: number;
    jenisLaporan: string;
    jenisLaporanLabel: string;
    periode: string;
    status: string;
    statusLabel: string;
    deadline: string | null;
    documents: {
        id: number;
        jenisDokumen: string;
        namaFile: string;
        driveFileUrl: string | null;
        uploadedAt: string;
    }[];
};

export type ClientDetail = {
    id: number;
    namaEntitas: string;
    jenisKlien: string;
    npwp: string | null;
    email: string;
    paket: { nama: string } | null;
    driveFolderId: string | null;
    taxReports: ClientTaxReport[];
};

export type RecapBulananRow = {
    no: number;
    namaEntitas: string;
    jenisKlien: string;
    jenisLaporan: string;
    deadline: string | null;
};

export type RecapBulananSection = {
    status: string;
    label: string;
    count: number;
    rows: RecapBulananRow[];
};

export type RecapBulanan = {
    periode: string;
    periodeLabel: string;
    printedAt: string;
    sections: RecapBulananSection[];
    total: number;
};

export type RecapTahunanMatrixRow = {
    no: number;
    namaEntitas: string;
    cells: (string | null)[];
    selesaiCount: number;
};

export type RecapTahunanAnnualRow = {
    no: number;
    namaEntitas: string;
    jenisLaporan: string;
    status: string;
    statusLabel: string;
    deadline: string | null;
};

export type RecapTahunan = {
    year: number;
    months: string[];
    printedAt: string;
    rows: RecapTahunanMatrixRow[];
    monthlyReported: number[];
    totalMasa: number;
    annualReports: RecapTahunanAnnualRow[];
};
export type RingkasanKlien = {
    menungguDokumen: number;
    diproses: number;
    dilaporkan: number;
    deadlineTerdekat: string | null;
};

export type LaporanAktifItem = {
    id: number;
    jenisLaporan: string;
    periode: string;
    status: string;
    statusLabel: string;
    deadline: string | null;
    dokumenCount: number;
};

export type LaporanDokumen = {
    id: number;
    jenisDokumen: string;
    namaFile: string;
    driveFileUrl: string | null;
    uploadedBy?: string | null;
    uploadedAt: string;
};

export type LaporanDetail = {
    id: number;
    jenisLaporan: string;
    periode: string;
    status: string;
    statusLabel: string;
    deadline: string | null;
    documents: LaporanDokumen[];
};

export type RiwayatItem = {
    id: number;
    jenisLaporan: string;
    periode: string;
    deadline: string | null;
    status: string;
    statusLabel: string;
};

export type RiwayatGroup = {
    tahun: number;
    items: RiwayatItem[];
};

export type ProfilKlien = {
    namaEntitas: string | null;
    jenisKlien: string | null;
    npwp: string | null;
    paket: { nama: string; harga: string } | null;
};

export type ClientIndexRow = {
    id: number;
    namaEntitas: string;
    jenisKlien: string;
    npwp: string | null;
    email: string;
    paket: string | null;
    laporanAktifCount: number;
};

export type Paginated<T> = {
    data: T[];
    meta: { currentPage: number; lastPage: number; total: number };
};

export type PackageRow = {
    id: number;
    namaPaket: string;
    deskripsi: string | null;
    jenisKlien: string;
    harga: number;
    klienCount: number;
};

export type InvoiceRow = {
    id: number;
    klien: string | null;
    periode: string;
    nominal: number;
    status: string;
    statusLabel: string;
};

export type NotificationLogRow = {
    id: number;
    klien: string | null;
    tipe: string;
    channel: string;
    status: string;
    statusLabel: string;
    sentAt: string | null;
    createdAt: string;
};
