;expand rom size to 24mbit
org $200000 : fill $100000

;edit rom header to use 24mbit
org $0001a0 : dd $00000000 : dd $002fffff

;updated compressed data pointers to new locations
org $0b0004 : dd $00200000 ;8x8 font
org $0b0624 : dd $00204000 ;title screen tiledata

;insert 8x8 font
org $200000
  db $00   ;compression type: none
  dw $2000 ;decompressed size
  incbin data/font8.bin

;insert title screen tilemap
org $0a4299 : incbin data/title_map.bin

;insert title screen tiledata
org $00204000
  db $00   ;compression type: none
  dw $1760 ;decompressed size
  incbin data/title.bin
