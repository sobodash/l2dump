#include <stdio.h>

typedef unsigned char  byte;
typedef unsigned short word;
typedef unsigned long  ulong;

byte *data;
ulong __width, __height;

byte getpixel(ulong x, ulong y) {
int __y;
  __y = (__height - 1) - y;
  if((x&1) == 0) {
    return (data[(__y*__width+x)>>1]>>4)&0x0f;
  } else {
    return (data[(__y*__width+x)>>1])&0x0f;
  }
}

void bmptobin(char *dfn, char *sfn, ulong width, ulong height) {
FILE *fp, *wr;
int x, y, x1, y1, z;
  __width  = width;
  __height = height;

  fp=fopen(sfn, "rb");
  if(!fp)return;
  fseek(fp, 0x76, SEEK_SET);
  wr=fopen(dfn, "wb");

  data=(byte*)malloc(width*height/2);
  fread(data, 1, width*height/2, fp);
  fclose(fp);

  for(y=0;y<height;y+=8) {
    for(x=0;x<width;x+=8) {
      for(y1=0;y1<8;y1++) {
        for(x1=0;x1<8;x1+=2) {
          z=getpixel(x+x1, y+y1)<<4;
          z|=getpixel(x+x1+1, y+y1);
          fputc(z, wr);
        }
      }
    }
  }

  fclose(wr);
}

int main() {
  bmptobin("data/font8.bin", "data/font8.bmp", 128, 128);
  bmptobin("data/title_raw.bin", "data/title.bmp", 224,  64);
  return 0;
}
