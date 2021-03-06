#include <stdio.h>

typedef unsigned char  byte;
typedef unsigned short word;
typedef unsigned long  ulong;

byte bmp16_header[]={
  0x42,0x4D,0x76,0x10,0x00,0x00,0x00,0x00,0x00,0x00,0x76,0x00,0x00,0x00,0x28,0x00,
  0x00,0x00,0x80,0x00,0x00,0x00,0x80,0x00,0x00,0x00,0x01,0x00,0x04,0x00,0x00,0x00,
  0x00,0x00,0x00,0x10,0x00,0x00,0xC4,0x0E,0x00,0x00,0xC4,0x0E,0x00,0x00,0x00,0x00,
  0x00,0x00,0x00,0x00,0x00,0x00
};

byte palette_8x8font[]={
  0x00, 0x00, 0x00, 0x00,
  0xff, 0x00, 0x00, 0x00,
  0x3f, 0x3f, 0x3f, 0x00,
  0xff, 0xff, 0xff, 0x00,
  0xff, 0xff, 0xff, 0x00,
  0xbf, 0xbf, 0xbf, 0x00,
  0x00, 0xff, 0xff, 0x00,
  0xff, 0x00, 0x00, 0x00,
  0xff, 0x00, 0xff, 0x00,
  0xff, 0xff, 0x00, 0x00,
  0x00, 0x00, 0x7f, 0x00,
  0x00, 0x7f, 0x00, 0x00,
  0x00, 0x7f, 0x7f, 0x00,
  0x7f, 0x00, 0x00, 0x00,
  0x7f, 0x00, 0x7f, 0x00,
  0x7f, 0x7f, 0x00, 0x00,
};

byte *data;
ulong __width, __height;

void setpixel(ulong x, ulong y, byte c) {
int __y;
  __y = (__height - 1) - y;
  c&=0x0f;
  if((x&1) == 0) {
    data[(__y*__width+x)>>1] =c<<4;
  } else {
    data[(__y*__width+x)>>1]|=c;
  }
}

void bintobmp(char *dfn, char *sfn, ulong width, ulong height, byte *palette) {
FILE *fp, *wr;
int x, y, x1, y1, z;
  __width  = width;
  __height = height;

  fp=fopen(sfn, "rb");
  if(!fp)return;
  wr=fopen(dfn, "wb");

  data=(byte*)malloc(width*height/2);
  memset(data, 0, width*height/2);

  for(y=0;y<height;y+=8) {
    for(x=0;x<width;x+=8) {
      for(y1=0;y1<8;y1++) {
        for(x1=0;x1<8;x1+=2) {
          z=fgetc(fp);
          setpixel(x+x1, y+y1, (z>>4));
          setpixel(x+x1+1, y+y1, z);
        }
      }
    }
  }

  bmp16_header[0x12]=width;
  bmp16_header[0x13]=width>>8;
  bmp16_header[0x16]=height;
  bmp16_header[0x17]=height>>8;

  fwrite(bmp16_header, 1, 0x36, wr);
  fwrite(palette, 1, 0x40, wr);
  fwrite(data, 1, width*height/2, wr);

  fclose(fp);
  fclose(wr);
}

int main() {
  bintobmp("data/font8j.bmp", "data/font8j.bin", 128, 128, palette_8x8font);
  return 0;
}
