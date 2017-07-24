table data/font8.tbl

org $0a3941 : db ' '
org $0a39a3 : db '         '
org $0a39dc : db '         '
org $0a3a17 : db '         '
org $0a3a52 : db '         '
org $0a3a5e : db ' '
org $0a3a91 : db '         '

org $0a394b : db 'A B C D E  a b c d e  . . . . .'
org $0a397a : db 'F G H I J  f g h i j  . . . . .'
org $0a39b5 : db 'K L M N O  k l m n o  . . . .'
org $0a39ee : db 'P Q R S T  p q r s t  0 1 2 3 4'
org $0a3a29 : db 'U V W X Y  u v w x y  5 6 7 8 9'
org $0a3a68 : db 'Z . . . .  z . . . .  Next  Back'

org $0a3a91
  db $fe,$04,$20
  db '     '
  db $fe,$0e,$20
  db $06,$07,$06,$07
  db '  '
  db '. . . . .  . . . . .  Done'
  db $fe,$08,$20

org $0a450f
  db 'Press START Button',$ff
