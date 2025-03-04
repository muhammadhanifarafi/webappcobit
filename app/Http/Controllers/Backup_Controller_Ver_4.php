        // Without ID
        if($id == 4){                       
            // $dataQuery = DB::table('flag_status')
            // ->select(
            //     'flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', 'tpp.pic',
            //     DB::raw("
            //         CASE
                        
            //             " . ($id2 == 1 ? "
            //             WHEN DATEDIFF(NOW(), tpk.tanggal_mulai) / DATEDIFF(tpk.tanggal_selesai, tpk.tanggal_mulai) < 0.8 
            //             AND NOW() BETWEEN tpk.tanggal_mulai AND tpk.tanggal_selesai 
            //             THEN 'Hijau'
            //             " : "") . "
                        
            //             " . ($id2 == 2 ? "
            //             WHEN DATEDIFF(NOW(), tpk.tanggal_mulai) / DATEDIFF(tpk.tanggal_selesai, tpk.tanggal_mulai) >= 0.8 
            //             AND NOW() BETWEEN tpk.tanggal_mulai AND tpk.tanggal_selesai 
            //             THEN 'Kuning'
            //             " : "") . "

            //             " . ($id2 == 3 ? "
            //             WHEN NOW() > tpk.tanggal_selesai 
            //             THEN 'Merah'
            //             " : "") . "
                        
            //             " . ($id2 == 4 ? "
            //             WHEN DATEDIFF(NOW(), tpk.tanggal_mulai) / DATEDIFF(tpk.tanggal_selesai, tpk.tanggal_mulai) >= 1.0
            //             THEN 'Putih'
            //             " : "") . "
                        
            //             ELSE 'Belum Dimulai'
            //         END AS status_proyek
            //     ")
            // )
            // ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
            // ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
            // ->leftJoin('trx_perencanaan_kebutuhan as tpk', 'tpk.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
            // ->distinct();
            
            // // Tambahkan kondisi having berdasarkan nilai $id2
            // if ($id2 == 1) {
            //     $dataQuery->havingRaw("status_proyek = 'Hijau'");
            // }else if ($id2 == 2) {
            //     $dataQuery->havingRaw("status_proyek = 'Kuning'");
            // } else if ($id2 == 3) {
            //     $dataQuery->havingRaw("status_proyek = 'Merah'");
            // }else if ($id2 == 4) {
            //     $dataQuery->havingRaw("status_proyek = 'Putih'");
            // }

            // $data = $dataQuery->get()
            $data = DB::table('flag_status')
                    ->select(
                        'flag_status.id_permintaan', 
                        'tpp.nomor_dokumen', 
                        'tpp.latar_belakang', 
                        'tpp.tujuan', 
                        'tpp2.progress', 
                        'tpp.pic',
                        DB::raw('MAX(flag_status.flag) AS max_flag'),
                        
                        // Menambahkan kolom status proyek dengan bobot berdasarkan waktu dan progress
                        DB::raw("
                            CASE
                                " . ($id2 == 1 ? "
                                WHEN DATEDIFF(NOW(), tpk.tanggal_mulai) / DATEDIFF(tpk.tanggal_selesai, tpk.tanggal_mulai) < 0.8 
                                AND NOW() BETWEEN tpk.tanggal_mulai AND tpk.tanggal_selesai THEN 'Hijau'
                                " : "") . "
                                
                                " . ($id2 == 2 ? "
                                WHEN DATEDIFF(NOW(), tpk.tanggal_mulai) / DATEDIFF(tpk.tanggal_selesai, tpk.tanggal_mulai) >= 0.8 
                                AND NOW() BETWEEN tpk.tanggal_mulai AND tpk.tanggal_selesai THEN 'Kuning'
                                " : "") . "

                                " . ($id2 == 3 ? "
                                WHEN NOW() > tpk.tanggal_selesai THEN 'Merah'
                                " : "") . "
                                
                                " . ($id2 == 4 ? "
                                WHEN DATEDIFF(NOW(), tpk.tanggal_mulai) / DATEDIFF(tpk.tanggal_selesai, tpk.tanggal_mulai) >= 1.0 THEN 'Putih'
                                " : "") . "

                                ELSE 'Belum Dimulai'
                            END AS status_proyek,
                            
                            CASE
                                " . ($id2 == 1 ? "
                                WHEN DATEDIFF(NOW(), tpk.tanggal_mulai) / DATEDIFF(tpk.tanggal_selesai, tpk.tanggal_mulai) < 0.8 
                                AND NOW() BETWEEN tpk.tanggal_mulai AND tpk.tanggal_selesai THEN 1 -- Bobot untuk Hijau
                                " : "") . "

                                " . ($id2 == 2 ? "
                                WHEN DATEDIFF(NOW(), tpk.tanggal_mulai) / DATEDIFF(tpk.tanggal_selesai, tpk.tanggal_mulai) >= 0.8 
                                AND NOW() BETWEEN tpk.tanggal_mulai AND tpk.tanggal_selesai THEN 2 -- Bobot untuk Kuning
                                " : "") . "

                                " . ($id2 == 3 ? "
                                WHEN NOW() > tpk.tanggal_selesai THEN 3 -- Bobot untuk Merah
                                " : "") . "

                                " . ($id2 == 4 ? "
                                WHEN DATEDIFF(NOW(), tpk.tanggal_mulai) / DATEDIFF(tpk.tanggal_selesai, tpk.tanggal_mulai) >= 1.0 THEN 4 -- Bobot untuk Putih
                                " : "") . "
                                
                                ELSE 0 -- Bobot untuk Belum Dimulai
                            END AS bobot_status_proyek
                        ")
                    )
                    ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                    ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                    ->leftJoin('trx_perencanaan_kebutuhan as tpk', 'tpk.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan') // Tambahkan join ke tabel tpk
                    ->groupBy(
                        'flag_status.id_permintaan', 
                        'tpp.nomor_dokumen', 
                        'tpp.latar_belakang', 
                        'tpp.tujuan', 
                        'tpp2.progress', 
                        'tpp.pic',
                        'tpk.tanggal_mulai', 
                        'tpk.tanggal_selesai',
                        'tpk.is_approve'
                    )
                    ->having(DB::raw('MAX(flag_status.flag)'), '=', 2)
                    ->distinct()
                    ->get();
                    
            return response()->json($data);

        }else if($id == 2){    
            if($id2 == 1){      
                $data = DB::table('flag_status')
                ->select('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', 'tpp2.progress', 'tpp.pic', DB::raw('MAX(flag_status.flag) AS max_flag'))
                ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                ->groupBy('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', 'tpp2.progress', 'tpp.pic')
                ->having(DB::raw('MAX(flag_status.flag)'), '=', 2)
                ->get();

                return response()->json($data);
            }else if($id2 == 2){      
                // Is Approve
                $data = DB::table('flag_status')
                ->select('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', 'tpp2.progress', 'tpp.pic')
                ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                ->where([
                    ['tpp2.is_approve', '=', 1], 
                    ['flag_status.flag', '=', 2]
                ])
                ->groupBy('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', 'tpp2.progress', 'tpp.pic')
                ->get();

                return response()->json($data);
            }else if($id2 == 3){      
                // Not Approve
                $data = DB::table('flag_status')
                ->select('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', 'tpp2.progress', 'tpp.pic')
                ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                ->where([
                    ['tpp2.is_approve', '=', NULL], 
                    ['flag_status.flag', '=', 2]
                ])
                ->groupBy('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', 'tpp2.progress', 'tpp.pic')
                ->get();

                return response()->json($data);
            }
            
        }else if($id == 3){
            if($id2 == 1){      
                $data = DB::table('flag_status')
                ->select('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', 'tpp3.progress', 'tpp.pic', DB::raw('MAX(flag_status.flag) AS max_flag'))
                ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                ->leftJoin('trx_perencanaan_proyek as tpp3', 'tpp3.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
                ->groupBy('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', 'tpp3.progress', 'tpp.pic')
                ->having(DB::raw('MAX(flag_status.flag)'), '=', 3)
                ->get();
    
                return response()->json($data);
            }else if($id2 == 2){      
                // Is Approve
                $data = DB::table('flag_status')
                ->select('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', 'tpp3.progress', 'tpp.pic')
                ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                ->leftJoin('trx_perencanaan_proyek as tpp3', 'tpp3.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
                ->where([
                    ['tpp3.is_approve', '=', 1], 
                    ['flag_status.flag', '=', 3]
                ])
                ->groupBy('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', 'tpp3.progress', 'tpp.pic')
                ->get();

                return response()->json($data);
            }else if($id2 == 3){      
                // Not Approve
                $data = DB::table('flag_status')
                ->select('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', 'tpp3.progress', 'tpp.pic')
                ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                ->leftJoin('trx_perencanaan_proyek as tpp3', 'tpp3.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
                ->where([
                    ['tpp3.is_approve', '=', NULL], 
                    ['flag_status.flag', '=', 3]
                ])
                ->groupBy('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', 'tpp3.progress', 'tpp.pic')
                ->get();

                return response()->json($data);
            }
        }else if($id == 12){
            $data = DB::table('flag_status')
            ->select('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', 'tpk.progress', 'tpp.pic', DB::raw('MAX(flag_status.flag) AS max_flag'))
            ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
            ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
            ->leftJoin('trx_perencanaan_kebutuhan as tpk', 'tpk.id_persetujuan_pengembangan', '=', 'tpp2.id_persetujuan_pengembangan')
            ->groupBy('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', 'tpk.progress', 'tpp.pic')
            ->having(DB::raw('MAX(flag_status.flag)'), '=', 4)
            ->get();

            return response()->json($data);
        }else if($id == 5){
            $data = DB::table('flag_status')
            ->select('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', 'tad.progress', 'tpp.pic', DB::raw('MAX(flag_status.flag) AS max_flag'))
            ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
            ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
            ->leftJoin('trx_analisis_desain as tad', 'tad.id_permintaan_pengembangan', '=', 'tpp2.id_permintaan_pengembangan')
            ->groupBy('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', 'tad.progress', 'tpp.pic')
            ->having(DB::raw('MAX(flag_status.flag)'), '=', 5)
            ->get();

            return response()->json($data);
        }else if($id == 6){
            $data = DB::table('flag_status')
            ->select('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', 'tqat.progress', 'tpp.pic', DB::raw('MAX(flag_status.flag) AS max_flag'))
            ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
            ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
            ->leftJoin('trx_quality_assurance_testing as tqat', 'tqat.id_permintaan_pengembangan', '=', 'tpp2.id_permintaan_pengembangan')
            ->groupBy('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', 'tqat.progress', 'tpp.pic')
            ->having(DB::raw('MAX(flag_status.flag)'), '=', 6)
            ->get();

            return response()->json($data);
        }else if($id == 7){
            $data = DB::table('flag_status')
            ->select('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', 'tuat.progress', 'tpp.pic', DB::raw('MAX(flag_status.flag) AS max_flag'))
            ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
            ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
            ->leftJoin('trx_user_acceptance_testing as tuat', 'tuat.id_permintaan_pengembangan', '=', 'tpp2.id_permintaan_pengembangan')
            ->groupBy('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', 'tuat.progress', 'tpp.pic')
            ->having(DB::raw('MAX(flag_status.flag)'), '=', 7)
            ->get();

            return response()->json($data);
        }else if($id == 8){
                $data = DB::table('flag_status')
                ->select('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', 'tsta.progress', 'tpp.pic', DB::raw('MAX(flag_status.flag) AS max_flag'))
                ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                ->leftJoin('trx_persetujuan_pengembangan as tpp2', 'tpp2.id_permintaan_pengembangan', '=', 'tpp.id_permintaan_pengembangan')
                ->leftJoin('trx_serah_terima_aplikasi as tsta', 'tsta.id_permintaan_pengembangan', '=', 'tpp2.id_permintaan_pengembangan')
                ->groupBy('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', 'tsta.progress', 'tpp.pic')
                ->having(DB::raw('MAX(flag_status.flag)'), '=', 8)
                ->get();

                return response()->json($data);
        }else if($id == 1){
            if($id2 == 1){      
                $data = DB::table('flag_status')
                ->select('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', 'tpp.progress', 'tpp.pic', DB::raw('MAX(flag_status.flag) AS max_flag'))
                ->leftJoin('trx_permintaan_pengembangan as tpp', 'tpp.id_permintaan_pengembangan', '=', 'flag_status.id_permintaan')
                ->groupBy('flag_status.id_permintaan', 'tpp.nomor_dokumen', 'tpp.latar_belakang', 'tpp.tujuan', 'tpp.progress', 'tpp.pic')
                ->having(DB::raw('MAX(flag_status.flag)'), '=', 1)
                ->get();

                return response()->json($data);
            }else if($id2 == 2){      
                // Is Approve
                $data = PermintaanPengembangan::where('is_approve', 1)->get();

                return response()->json($data);
            }else if($id2 == 3){      
                // Not Approve
                $data = PermintaanPengembangan::where('is_approve', NULL)->get();

                return response()->json($data);
            }
        }