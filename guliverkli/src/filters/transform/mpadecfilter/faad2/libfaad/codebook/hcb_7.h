/*
** FAAD2 - Freeware Advanced Audio (AAC) Decoder including SBR decoding
** Copyright (C) 2003 M. Bakker, Ahead Software AG, http://www.nero.com
**  
** This program is free software; you can redistribute it and/or modify
** it under the terms of the GNU General Public License as published by
** the Free Software Foundation; either version 2 of the License, or
** (at your option) any later version.
** 
** This program is distributed in the hope that it will be useful,
** but WITHOUT ANY WARRANTY; without even the implied warranty of
** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
** GNU General Public License for more details.
** 
** You should have received a copy of the GNU General Public License
** along with this program; if not, write to the Free Software 
** Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
**
** Any non-GPL usage of this software or parts of this software is strictly
** forbidden.
**
** Commercial non-GPL licensing of this software is possible.
** For more info contact Ahead Software through Mpeg4AAClicense@nero.com.
**
** $Id: hcb_7.h 441 2005-11-01 21:41:43Z gabest $
**/

/* Binary search huffman table HCB_7 */


static hcb_bin_pair hcb7[] = {
    { /*  0 */ 0, { 1, 2 } },
    { /*  1 */ 1, { 0, 0 } },
    { /*  2 */ 0, { 1, 2 } },
    { /*  3 */ 0, { 2, 3 } },
    { /*  4 */ 0, { 3, 4 } },
    { /*  5 */ 1, { 1, 0 } },
    { /*  6 */ 1, { 0, 1 } },
    { /*  7 */ 0, { 2, 3 } },
    { /*  8 */ 0, { 3, 4 } },
    { /*  9 */ 1, { 1,  1 } },
    { /* 10 */ 0, { 3, 4 } },
    { /* 11 */ 0, { 4, 5 } },
    { /* 12 */ 0, { 5, 6 } },
    { /* 13 */ 0, { 6, 7 } },
    { /* 14 */ 0, { 7, 8 } },
    { /* 15 */ 0, { 8, 9 } },
    { /* 16 */ 0, { 9, 10 } },
    { /* 17 */ 0, { 10, 11 } },
    { /* 18 */ 0, { 11, 12 } },
    { /* 19 */ 1, { 2,  1 } },
    { /* 20 */ 1, { 1,  2 } },
    { /* 21 */ 1, { 2,  0 } },
    { /* 22 */ 1, { 0,  2 } },
    { /* 23 */ 0, { 8, 9 } },
    { /* 24 */ 0, { 9, 10 } },
    { /* 25 */ 0, { 10, 11 } },
    { /* 26 */ 0, { 11, 12 } },
    { /* 27 */ 0, { 12, 13 } },
    { /* 28 */ 0, { 13, 14 } },
    { /* 29 */ 0, { 14, 15 } },
    { /* 30 */ 0, { 15, 16 } },
    { /* 31 */ 1, { 3,  1 } },
    { /* 32 */ 1, { 1,  3 } },
    { /* 33 */ 1, { 2,  2 } },
    { /* 34 */ 1, { 3,  0 } },
    { /* 35 */ 1, { 0,  3 } },
    { /* 36 */ 0, { 11, 12 } },
    { /* 37 */ 0, { 12, 13 } },
    { /* 38 */ 0, { 13, 14 } },
    { /* 39 */ 0, { 14, 15 } },
    { /* 40 */ 0, { 15, 16 } },
    { /* 41 */ 0, { 16, 17 } },
    { /* 42 */ 0, { 17, 18 } },
    { /* 43 */ 0, { 18, 19 } },
    { /* 44 */ 0, { 19, 20 } },
    { /* 45 */ 0, { 20, 21 } },
    { /* 46 */ 0, { 21, 22 } },
    { /* 47 */ 1, { 2,  3 } },
    { /* 48 */ 1, { 3,  2 } },
    { /* 49 */ 1, { 1,  4 } },
    { /* 50 */ 1, { 4,  1 } },
    { /* 51 */ 1, { 1,  5 } },
    { /* 52 */ 1, { 5,  1 } },
    { /* 53 */ 1, { 3,  3 } },
    { /* 54 */ 1, { 2,  4 } },
    { /* 55 */ 1, { 0,  4 } },
    { /* 56 */ 1, { 4,  0 } },
    { /* 57 */ 0, { 12, 13 } },
    { /* 58 */ 0, { 13, 14 } },
    { /* 59 */ 0, { 14, 15 } },
    { /* 60 */ 0, { 15, 16 } },
    { /* 61 */ 0, { 16, 17 } },
    { /* 62 */ 0, { 17, 18 } },
    { /* 63 */ 0, { 18, 19 } },
    { /* 64 */ 0, { 19, 20 } },
    { /* 65 */ 0, { 20, 21 } },
    { /* 66 */ 0, { 21, 22 } },
    { /* 67 */ 0, { 22, 23 } },
    { /* 68 */ 0, { 23, 24 } },
    { /* 69 */ 1, { 4,  2 } },
    { /* 70 */ 1, { 2,  5 } },
    { /* 71 */ 1, { 5,  2 } },
    { /* 72 */ 1, { 0,  5 } },
    { /* 73 */ 1, { 6,  1 } },
    { /* 74 */ 1, { 5,  0 } },
    { /* 75 */ 1, { 1,  6 } },
    { /* 76 */ 1, { 4,  3 } },
    { /* 77 */ 1, { 3,  5 } },
    { /* 78 */ 1, { 3,  4 } },
    { /* 79 */ 1, { 5,  3 } },
    { /* 80 */ 1, { 2,  6 } },
    { /* 81 */ 1, { 6,  2 } },
    { /* 82 */ 1, { 1,  7 } },
    { /* 83 */ 0, { 10, 11 } },
    { /* 84 */ 0, { 11, 12 } },
    { /* 85 */ 0, { 12, 13 } },
    { /* 86 */ 0, { 13, 14 } },
    { /* 87 */ 0, { 14, 15 } },
    { /* 88 */ 0, { 15, 16 } },
    { /* 89 */ 0, { 16, 17 } },
    { /* 90 */ 0, { 17, 18 } },
    { /* 91 */ 0, { 18, 19 } },
    { /* 92 */ 0, { 19, 20 } },
    { /* 93 */ 1, { 3,  6 } },
    { /* 94 */ 1, { 0,  6 } },
    { /* 95 */ 1, { 6,  0 } },
    { /* 96 */ 1, { 4,  4 } },
    { /* 97 */ 1, { 7,  1 } },
    { /* 98 */ 1, { 4,  5 } },
    { /* 99 */ 1, { 7,  2 } },
    { /* 00 */ 1, { 5,  4 } },
    { /* 01 */ 1, { 6,  3 } },
    { /* 02 */ 1, { 2,  7 } },
    { /* 03 */ 1, { 7,  3 } },
    { /* 04 */ 1, { 6,  4 } },
    { /* 05 */ 1, { 5,  5 } },
    { /* 06 */ 1, { 4,  6 } },
    { /* 07 */ 1, { 3,  7 } },
    { /* 08 */ 0, { 5, 6 } },
    { /* 09 */ 0, { 6, 7 } },
    { /* 10 */ 0, { 7, 8 } },
    { /* 11 */ 0, { 8, 9 } },
    { /* 12 */ 0, { 9, 10 } },
    { /* 13 */ 1, { 7,  0 } },
    { /* 14 */ 1, { 0,  7 } },
    { /* 15 */ 1, { 6,  5 } },
    { /* 16 */ 1, { 5,  6 } },
    { /* 17 */ 1, { 7,  4 } },
    { /* 18 */ 1, { 4,  7 } },
    { /* 19 */ 1, { 5,  7 } },
    { /* 20 */ 1, { 7,  5 } },
    { /* 21 */ 0, { 2, 3 } },
    { /* 22 */ 0, { 3, 4 } },
    { /* 23 */ 1, { 7,  6 } },
    { /* 24 */ 1, { 6,  6 } },
    { /* 25 */ 1, { 6,  7 } },
    { /* 26 */ 1, { 7,  7 } }
};
