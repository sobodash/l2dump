#include <stdio.h>

#define null 0xffffffff

typedef unsigned char  byte;
typedef unsigned short word;
typedef unsigned long  ulong;

FILE *fp, *wr, *map;
byte *data;

byte tileset[0xe0][32];
byte tile_count = 0;

ulong find_tile(ulong num) {
int i, l, m;
  for(i=0;i<tile_count;i++) {
    m=0;
    for(l=0;l<32;l++) {
      if(tileset[i][l] == data[num*32+l])m++;
    }
    if(m == 32)return i;
  }
  return null;
}

void insert_tile(ulong num) {
int i;
ulong found = find_tile(num);
  if(found != null) {
    fputc(found, map);
  } else {
    for(i=0;i<32;i++) {
      tileset[tile_count][i] = data[num*32+i];
    }
    fputc(tile_count++, map);
  }
}

int main() {
int i;
  fp=fopen("data/title_raw.bin", "rb");
  if(!fp)return 0;
  data=(byte*)malloc(0x1c00);
  fread(data, 1, 0x1c00, fp);
  fclose(fp);

  memset(tileset, 0, 0xe0*32);

  map=fopen("data/title_map_raw.bin", "wb");
  for(i=0;i<0xe0;i++)insert_tile(i);
  fclose(map);

  wr=fopen("data/title.bin", "wb");
  for(i=0;i<tile_count;i++) {
    fwrite(tileset[i], 1, 32, wr);
  }
  fclose(wr);

//cut off 5 bytes from the bottom right, it will
//still display properly, as these tiles are all
//blank in title.bmp. I do this because there is
//no room for all 0xe0 bytes in the rom.
  fp=fopen("data/title_map_raw.bin", "rb");
  fread(data, 1, 0xdb, fp);
  fclose(fp);

  wr=fopen("data/title_map.bin", "wb");
  fwrite(data, 1, 0xdb, wr);
  fclose(wr);

  return 0;
}
