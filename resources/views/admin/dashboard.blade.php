<x-admin-layout title="Dashboard">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-body">
                    <h4 class="mb-3">📋 Daftar Kunjungan via QR</h4>

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Pengunjung</th>
                                <th>Waktu Kunjungan (WIB)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($kunjungan as $i => $item)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ $item->user->name ?? '-' }}</td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($item->waktu_kunjungan)
                                            ->timezone('Asia/Jakarta')
                                            ->translatedFormat('d F Y, H:i:s') }} WIB
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">Belum ada kunjungan via QR.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <hr>

                    <h5 class="mt-4">📊 Statistik Kunjungan per Hari (via QR)</h5>
                    <p>Total Kunjungan: <strong>{{ $kunjunganHariIni }}</strong></p>

                    @php
                        $kunjunganPerHari = collect($kunjungan)
                            ->groupBy(fn($item) => \Carbon\Carbon::parse($item->waktu_kunjungan)->format('Y-m-d'))
                            ->map(fn($group) => $group->count())
                            ->sortKeys()
                            ->toArray();
                    @endphp

                    <canvas id="kunjunganChart" height="120" style="max-height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        {{-- Pakai Chart.js lokal --}}
        <script src="{{ asset('assets/js/chart.js') }}"></script>
        <script>
            const ctx = document.getElementById('kunjunganChart').getContext('2d');
            const kunjunganChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode(array_map(function($tgl) {
                        return \Carbon\Carbon::parse($tgl)->translatedFormat('d M Y');
                    }, array_keys($kunjunganPerHari))) !!},
                    datasets: [{
                        label: 'Jumlah Kunjungan per Hari',
                        data: {!! json_encode(array_values($kunjunganPerHari)) !!},
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        </script>
    @endpush
</x-admin-layout>